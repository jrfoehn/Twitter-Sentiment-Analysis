<?php
namespace App;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Database\Eloquent\Model;

class RequestTwitter extends TwitterOAuth {
    protected $connection;
    
    function __construct() {
        $this->connection = new TwitterOAuth(
            env('TWITTER_CONSUMER_KEY'),
            env('TWITTER_CONSUMER_SECRET'),
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET')
        );
    }
    
    function test() {
        return $this->connection->get("account/verify_credentials");
    }
    
    function searchHashtag($hashtag, $cnt, $streaming, $result_type, $until, $lang, $geocode) {
        $restNumber = $cnt;
        $result = null;
        $numberReturn;
        $max_id = '0';
        $count;
        
        // temporarily extend PHP execution time limit
        set_time_limit(300);
        ini_set('max_execution_time', 300);
        
        // proceed search via REST API
        while ($restNumber > 0) {
            if ($restNumber > 100) {
                $count = 100;
            } else {
                $count = $restNumber;
            }
            
            // request tweets from twitter search API
            $res = RequestTwitter::searchHastagRestAPI($hashtag, $count, $max_id, $result_type, $until, $lang, $geocode);
            
            if (!isset($res->statuses)) {
                // wrong results or reached rate limits
                break;
            }
            //var_dump("Returned " . count($res->statuses));
            
            $numberReturn = count($res->statuses);
            if ($numberReturn == 0) {
                // no or no more results, quit loop
                break;
            }
            
            // merge the response into results
            if ($result == null) {
                $result = $res;
            } else {
                $result->statuses = array_merge($result->statuses, $res->statuses);
            }
            //var_dump("Merged " . count($result->statuses));
            
            $restNumber -= $numberReturn;
            //var_dump("Rested:" . $restNumber);
            if (isset($res->search_metadata->next_results)) {
                // parse and retrieve the max id returned by twitter
                $parts = explode('&', str_replace('?', '', $res->search_metadata->next_results));
                $max_id = explode('=', $parts[0])[1];
            } else {
                break;
            }
        }
        
        // proceed search via Streaming API
        if ($restNumber > 0 && $streaming) {
            $res = self::searchHastagStreamingAPI($hashtag, $restNumber);
            if ($result == null) {
                $result = new stdClass();
                $result->statuses = $res;
            } else {
                $result->statuses = array_merge($result->statuses, $res);
            }
        }
        
        // revert PHP execution time limit
        set_time_limit(60);
        ini_set('max_execution_time', 60);
        
        return $result;
    }
    
    function searchHastagRestAPI($hashtag, $cnt, $max_id, $result_type, $until, $lang, $geocode) {
        return $this->connection->get("search/tweets", array(
            "q" => $hashtag,
            "count" => $cnt,
            "max_id" => $max_id,
            "result_type" => $result_type,
            "until" => $until,
            "lang" => $lang,
            "geocode" => $geocode
        ));
    }
    
    function searchHastagStreamingAPI($hashtag, $cnt) {
        $consumer = new StreamingTwitter($hashtag, $cnt);
        $consumer->consume();
        return $consumer->getResults();
    }
}

// END /lib/RequestOpenWeatherMap.class.php
