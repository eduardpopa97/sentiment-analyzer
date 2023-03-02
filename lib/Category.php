<?php
class Category {
    private $index = array();
    private $classes = array('Business', 'Entertainment', 'Politics', 'Sport', 'Tech');
    private $classTokCounts = array('Business' => 0, 'Entertainment' => 0, 'Politics' => 0, 'Sport' => 0, 'Tech' => 0);
    private $tokCount = 0;
    private $classDocCounts = array('Business' => 0, 'Entertainment' => 0, 'Politics' => 0, 'Sport' => 0, 'Tech' => 0);
    private $docCount = 0;
    private $prior = array('Business' => 0.2, 'Entertainment' => 0.2, 'Politics' => 0.2, 'Sport' => 0,2, 'Tech' => 0.2);

    public function trainCategory($file, $class, $limit = 0) {
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
    
    public function classifyText($document) {
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

                            $classScores[$class] *= ($count + 1)/ 
                                    ($this->classTokCounts[$class] + $this->tokCount);
                        
                    }
                    $classScores[$class] = $this->prior[$class] * $classScores[$class];
            }
            
            arsort($classScores);
            return key($classScores);
    }

    public function categoryScores($document) {
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
}

?>