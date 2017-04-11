<?php
namespace App;
use OauthPhirehose;
use Illuminate\Database\Eloquent\Model;
/**
 * A class extending "Phirehose" library to handle the consuming of Twitter's streaming API.
 *
 * @author Yihe Wang <tinayihe39@gmail.com>
 * @author Yipeng Huang <huang.ypeng@gmail.com>
 */
class StreamingTwitter extends OauthPhirehose {
    
    /**
     * Content phrase that should be included in statuses.
     *
     * @var string
     */
    private $track = '';
    
    /**
     * Max number of statuses to consume.
     *
     * @var integer
     */
    private $nbStatusesMax = 0;
    
    /**
     * Counter of consumed statuses.
     *
     * @var integer
     */
    private $nbStatuses = 0;
    
    /**
     * Temporary holder of consumed statuses.
     *
     * @var array
     */
    private $statuses = array();
    
    /**
     * End time of streaming consumption.
     *
     * @var float
     */
    private $endDateTime;
    
    /**
     * Constructor.
     *
     * @param int $nbStatusesMax the max number of statuses to consume
     * @param float $endDateTime the end UNIX time in second
     */
    public function __construct($query, $nbStatusesMax, $endDateTime = null) {
        // Call the constructor with keys and the filter method
        parent::__construct(
            env('TWITTER_ACCESS_TOKEN'),
            env('TWITTER_ACCESS_TOKEN_SECRET'),
            Phirehose::METHOD_FILTER
        );
        
        $this->consumerKey = env('TWITTER_CONSUMER_KEY');
        $this->consumerSecret = env('TWITTER_CONSUMER_SECRET');
        
        $this->track = self::query2track($query);
        
        // Set tracking query within streaming consumption
        $this->setTrack($this->track);
        
        // Set max number of statuses to consume
        $this->nbStatusesMax = $nbStatusesMax;
        
        // Set streaming end time
        if ($endDateTime == null) {
            $endDateTime = microtime(true) + env('DEFAULT_STREAMING_DURATION');
        }
        $this->endDateTime = $endDateTime;
    }
    
    /**
     * Overrided from Phirehose
     * Temporarily set the PHP time limit and max execution time to infinity.
     * The time limit should be revert at the moment of shutting down.
     * Then just call the consume() method of Phirehose to proceed streaming consumption.
     */
    public function consume($reconnect = TRUE) {
        // Temporarily discard the php time limit
        set_time_limit(0);
        ini_set('max_execution_time', 0);
        
        parent::consume();
    }
    
    /**
     * Overrided from Phirehose.
     * This method serves to treat every status coming in from the Streaming API.
     * The prefix "enqueue" is not exactly what this method do, in fact, this method is generally
     *   in charge of the format and the storage of every status.
     *
     * @param string $status the consumed status in JSON format
     * @see Phirehose::consume()
     */
    public function enqueueStatus($status) {
        // Cast the obtained status to php object
        $statusObj = json_decode($status);
        
        // "Enqueue" the data
        // In fact, there is no concept of queue, it's about the storage
        if ($this->validateInputStatus($statusObj)) {
            $this->statuses[] = $statusObj;
            $this->nbStatuses++;
        }
    }
    
    /**
     * Overrided from Phirehose.
     * This method is called every serveral seconds in the "consume" method of Phirehose.
     * As the "consume" method is continuous, the call of such "disconnect" method is impossible
     *   in a non-blocking way. So this method could treat something like the stop condition.
     *
     * @see Phirehose::consume()
     */
    protected function checkFilterPredicates() {
        $stopped = ($this->nbStatuses >= $this->nbStatusesMax) ||
            ($this->endDateTime != null && microtime(true) > $this->endDateTime);
        
        if ($stopped) {
            $this->shutdown();
        }
    }
    
    /**
     * This method serves to cut down the connection with streaming endpoint and with MongoDB.
     */
    private function shutdown() {
        // End the connection to Streaming API
        $this->disconnect();
        
        // Revert the php time limit
        set_time_limit(60);
        ini_set('max_execution_time', 60);
    }
    
    /**
     * This method is a validation of income status decoded from JSON.
     * In our case, a status should be valid at the level of Twitter, with id, timestamps...,
     *   then it should be also geo-enabled with a valid pair of GPS coordinates.
     */
    private function validateInputStatus($statusObj) {
        return !empty($statusObj) &&
            isset($statusObj->id_str) &&
            isset($statusObj->text) &&
            isset($statusObj->created_at) &&
            isset($statusObj->user) && isset($statusObj->user->id_str) &&
            isset($statusObj->entities->hashtags) && !empty($statusObj->entities->hashtags);
    }
    
    private function query2track($query) {
        return explode(' OR ', str_replace(' or ', ' OR ', $query));
    }
    
    public function getResults() {
        return $this->statuses;
    }
    
}

// END /lib/GeoFilteredStreaming.class.php
