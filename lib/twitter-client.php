<?php

define('TWITTER_API_TIMEOUT', 5 );  
define('TWITTER_API_USERAGENT', 'PHP/'.PHP_VERSION.'; http://github.com/timwhitlock/php-twitter-api' );  
define('TWITTER_API_BASE', 'https://api.twitter.com/1.1' );
define('TWITTER_OAUTH_REQUEST_TOKEN_URL', 'https://twitter.com/oauth/request_token');
define('TWITTER_OAUTH_AUTHORIZE_URL', 'https://twitter.com/oauth/authorize');
define('TWITTER_OAUTH_AUTHENTICATE_URL', 'https://twitter.com/oauth/authenticate');
define('TWITTER_OAUTH_ACCESS_TOKEN_URL', 'https://twitter.com/oauth/access_token');
 
class TwitterApiClient {

    private $Consumer;
    private $AccessToken;
    
    public function deauthorize(){
        $this->AccessToken = null;
        return $this;
    }

    public function set_oauth( $consumer_key, $consumer_secret, $access_key = '', $access_secret = '' ){
        $this->deauthorize();
        $this->Consumer = new TwitterOAuthToken( $consumer_key, $consumer_secret );
        if( $access_key && $access_secret ){
            $this->AccessToken = new TwitterOAuthToken( $access_key, $access_secret );
        }
        return $this;
    }
    
    public function call( $path, array $args = array(), $http_method = 'GET' ){
        $http = $this->rest_request( $path, $args, $http_method );
        $status = $http['status'];
        $data = json_decode( $http['body'], true );
        if( ! is_array($data) ){
            $err = array( 
                'message' => $http['error'], 
                'code' => -1 
            );
            
        }
        return $data;
    }

    private function rest_request( $path, array $args, $http_method ){
        $conf = array (
            'method' => $http_method,
        );
        $endpoint = TWITTER_API_BASE.'/'.$path.'.json';
        $params = new TwitterOAuthParams( $args );
        $params->set_consumer( $this->Consumer );
        $params->set_token( $this->AccessToken );
        $params->sign_hmac( $http_method, $endpoint );
        if( 'GET' === $http_method ){
            $endpoint .= '?'.$params->serialize();
        }
        else {
            $conf['body'] = $params->serialize();
        }
        $http = self::http_request( $endpoint, $conf );        
        
        $this->last_call = $path;
        if( isset($http['headers']['x-rate-limit-limit']) ) {
            $this->last_rate[$path] = array (
                'limit'     => (int) $http['headers']['x-rate-limit-limit'],
                'remaining' => (int) $http['headers']['x-rate-limit-remaining'],
                'reset'     => (int) $http['headers']['x-rate-limit-reset'],
            );
        }
        return $http;
    }    

    public static function http_request( $endpoint, array $conf ){
        $conf += array(
            'body' => '',
            'method'  => 'GET',
            'headers' => array(),
        );

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $endpoint );
        curl_setopt( $ch, CURLOPT_TIMEOUT, TWITTER_API_TIMEOUT );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, TWITTER_API_TIMEOUT );
        curl_setopt( $ch, CURLOPT_USERAGENT, TWITTER_API_USERAGENT );
        curl_setopt( $ch, CURLOPT_HEADER, true );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        
        switch ( $conf['method'] ) {
        case 'GET':
            break;
        case 'POST':
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, $conf['body'] );
            break;
      
        }
        
        foreach( $conf['headers'] as $key => $val ){
            $headers[] = $key.': '.$val;
        }
        if( isset($headers) ) {
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        }
        
        $response = curl_exec( $ch );
        if ( 60 === curl_errno($ch) ) { 
            curl_setopt( $ch, CURLOPT_CAINFO, __DIR__.'/ca-chain-bundle.crt');
            $response = curl_exec($ch);
        }
        $status = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        $headers = array();
        $body = '';
        if( $response && $status ){
            list( $header, $body ) = preg_split('/\r\n\r\n/', $response, 2 ); 
            if( preg_match_all('/^(Content[\w\-]+|X-Rate[^:]+):\s*(.+)/mi', $header, $r, PREG_SET_ORDER ) ){
                foreach( $r as $match ){
                    $headers[ strtolower($match[1]) ] = $match[2];
                }        
            }
            curl_close($ch);
        }
        else {
            $error = curl_error( $ch ) or 
            $error = 'No response from Twitter';
            is_resource($ch) and curl_close($ch);
         
        }
        return array (
            'body'    => $body,
            'status'  => $status,
            'headers' => $headers,
        );
    }

}

class TwitterOAuthToken {

    public $key;
    public $secret;
    public $verifier;
    public $user;

    public function __construct( $key, $secret = '' ){
        if( ! $key ){
           throw new Exception( 'Invalid OAuth token - Key required even if secret is empty' );
        }
        $this->key = $key;
        $this->secret = $secret;
        $this->verifier = '';
    }

}

class TwitterOAuthParams {
    
    private $args;
    private $consumer_secret;
    private $token_secret;
    
    private static function urlencode( $val ){
        return str_replace( '%7E', '~', rawurlencode($val) );
    }    
    
    public function __construct( array $args = array() ){
        $this->args = $args + array ( 
            'oauth_version' => '1.0',
        );
    }
    
    public function set_consumer( TwitterOAuthToken $Consumer ){
        $this->consumer_secret = $Consumer->secret;
        $this->args['oauth_consumer_key'] = $Consumer->key;
    }   
    
    public function set_token( TwitterOAuthToken $Token ){
        $this->token_secret = $Token->secret;
        $this->args['oauth_token'] = $Token->key;
    }   
    
    private function normalize(){
        $flags = SORT_STRING | SORT_ASC;
        ksort( $this->args, $flags );
        foreach( $this->args as $k => $a ){
            if( is_array($a) ){
                sort( $this->args[$k], $flags );
            }
        }
        return $this->args;
    }
    
    public function serialize(){
        $str = http_build_query( $this->args );
        $str = str_replace( array('+','%7E'), array('%20','~'), $str );
        return $str;
    }

    public function sign_hmac( $http_method, $http_rsc ){
        $this->args['oauth_signature_method'] = 'HMAC-SHA1';
        $this->args['oauth_timestamp'] = sprintf('%u', time() );
        $this->args['oauth_nonce'] = sprintf('%f', microtime(true) );
        unset( $this->args['oauth_signature'] );
        $this->normalize();
        $str = $this->serialize();
        $str = strtoupper($http_method).'&'.self::urlencode($http_rsc).'&'.self::urlencode($str);
        $key = self::urlencode($this->consumer_secret).'&'.self::urlencode($this->token_secret);
        $this->args['oauth_signature'] = base64_encode( hash_hmac( 'sha1', $str, $key, true ) );
        return $this->args;
    }
}
