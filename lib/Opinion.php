<?php
class Opinion {
    private $index = array();
    private $classes = array('Positive', 'Negative', 'Neutral');
    private $classTokCounts = array('Positive' => 0, 'Negative' => 0, 'Neutral' => 0);
    private $tokCount = 0;
    private $classDocCounts = array('Positive' => 0, 'Negative' => 0, 'Neutral' => 0);
    private $docCount = 0;
    private $prior = array('Positive' => 1.0/3.0, 'Negative' => 1.0/3.0, 'Neutral' => 1.0/3.0);

    public function train($file, $class, $limit = 0) {
            $fh = fopen($file, 'r');
            $i = 0;
            if(!in_array($class, $this->classes)) {
                    echo "Invalid class specified\n";
                    return;
            }
            while($line = fgets($fh)) {
                    if($limit > 0 && $i > $limit) {
                            break;
                    }
                    $i++;
                    $this->docCount++;
                    $this->classDocCounts[$class]++;
                    $tokens = $this->tokenise($line);
                    foreach($tokens as $token) {
                            if(!isset($this->index[$token][$class])) {
                                    $this->index[$token][$class] = 0;
                            }
                            $this->index[$token][$class]++;
                            $this->classTokCounts[$class]++;
                            $this->tokCount++;
                    }
                }
            fclose($fh);
        }
    
    public function classify($document) {
            // $this->prior['pos'] = $this->classDocCounts['pos'] / $this->docCount;
            // $this->prior['neg'] = $this->classDocCounts['neg'] / $this->docCount; 
            foreach($this->classes as $class) {
                $this->prior[$class] = $this->classDocCounts[$class] / $this->docCount;
            }
            $tokens = $this->tokenise($document);
            $classScores = array();

            foreach($this->classes as $class) {
                    $classScores[$class] = 1;
                    foreach($tokens as $token) {
                            $count = isset($this->index[$token][$class]) ? 
                                    $this->index[$token][$class] : 0;

                            $classScores[$class] *= ($count + 1) / 
                                    ($this->classTokCounts[$class] + $this->tokCount);
                        
                    }
                    $classScores[$class] = $this->prior[$class] * $classScores[$class];
            }
            
            arsort($classScores);
            return key($classScores);
    }

    public function sentimentScores($document) {
        // $this->prior['pos'] = $this->classDocCounts['pos'] / $this->docCount;
        // $this->prior['neg'] = $this->classDocCounts['neg'] / $this->docCount; 
        foreach($this->classes as $class) {
            $this->prior[$class] = $this->classDocCounts[$class] / $this->docCount;
        }
        $tokens = $this->tokenise($document);
        $classScores = array();

        foreach($this->classes as $class) {
                $classScores[$class] = 1;
                foreach($tokens as $token) {
                        $count = isset($this->index[$token][$class]) ? 
                                $this->index[$token][$class] : 0;

                        $classScores[$class] *= ($count + 1) / 
                                ($this->classTokCounts[$class] + $this->tokCount);
                }
                $classScores[$class] = $this->prior[$class] * $classScores[$class];
        }
        
        arsort($classScores);
        return $classScores;
}

    private function tokenise($document) {
            $document = strtolower($document);
            preg_match_all('/\w+/', $document, $matches);
            return $matches[0];
    }

    public function trainTime($file, $class, $limit = 0) {
        $start = microtime(true);
        $fh = fopen($file, 'r');
        $i = 0;
        if(!in_array($class, $this->classes)) {
                echo "Invalid class specified\n";
                return;
        }
        while($line = fgets($fh)) {
                if($limit > 0 && $i > $limit) {
                        break;
                }
                $i++;
                
                $this->docCount++;
                $this->classDocCounts[$class]++;
                $tokens = $this->tokenise($line);
                foreach($tokens as $token) {
                        if(!isset($this->index[$token][$class])) {
                                $this->index[$token][$class] = 0;
                        }
                        $this->index[$token][$class]++;
                        $this->classTokCounts[$class]++;
                        $this->tokCount++;
                }
            }
        fclose($fh);
        $end = microtime(true);
        $duration = ($end - $start);
        return $duration;
    }

    public function classifyTime($document) {
        $start = microtime(true);
        foreach($this->classes as $class) {
            $this->prior[$class] = $this->classDocCounts[$class] / $this->docCount;
        }
        $tokens = $this->tokenise($document);
        $classScores = array();

        foreach($this->classes as $class) {
                $classScores[$class] = 1;
                foreach($tokens as $token) {
                        $count = isset($this->index[$token][$class]) ? 
                                $this->index[$token][$class] : 0;

                        $classScores[$class] *= ($count + 1) / 
                                ($this->classTokCounts[$class] + $this->tokCount);
                    
                }
                $classScores[$class] = $this->prior[$class] * $classScores[$class];
        }
        
        arsort($classScores);
        $end = microtime(true);
        $duration = ($end - $start);
        return $duration;
}
}

?>