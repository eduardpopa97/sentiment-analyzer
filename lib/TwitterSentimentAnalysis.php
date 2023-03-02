<?php
include_once('twitter-client.php');
class TwitterSentimentAnalysis {
    
    protected $consumer_key; 
    protected $consumer_secret; 
    protected $access_key; 
    protected $access_secret; 
    
    public function __construct($consumer_key, $consumer_secret, $access_key, $access_secret){
        $this->consumer_key=$consumer_key;
        $this->consumer_secret=$consumer_secret;
        $this->access_key=$access_key;
        $this->access_secret=$access_secret;
    }
    
    public function sentimentAnalysis($twitterSearchParams) {
        $tweets=$this->getTweets($twitterSearchParams);
        return $this->findTweetsInfo($tweets);
    }
    
    protected function getTweets($twitterSearchParams) {
        $Client = new TwitterApiClient();
         
        $Client->set_oauth ($this->consumer_key, $this->consumer_secret, $this->access_key, $this->access_secret);

        $tweets = $Client->call('search/tweets', $twitterSearchParams, 'GET' ); 
        
        unset($Client);
        return $tweets;
    }
    
    protected function findTweetsInfo($tweets) {
        $results=array();
        foreach($tweets['statuses'] as $tweet) { 
            if(isset($tweet['metadata']['iso_language_code']) && $tweet['metadata']['iso_language_code']=='en') { 
        
                $results[]=array( 
                    'id'=>$tweet['id_str'],
                    'user'=>$tweet['user']['name'],
                    'text'=>$tweet['text'],
                    'url'=>'https://twitter.com/'.$tweet['user']['name'].'/status/'.$tweet['id_str'],
                );
            }
            
        }
        
        unset($tweets);
        return $results;
    }
}
?>
