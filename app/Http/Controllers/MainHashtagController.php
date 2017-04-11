<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\User;
use Illuminate\Support\Facades\Auth;
use OauthPhirehose;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\VarDumper\Cloner\Data;
use App\DataHandler;
use App\RequestTwitter;
use App\StreamingTwitter;

class MainHashtagController extends Controller
{

    private $dataHandler = null;
    private $requestTwitter = null;
    private $hashtagSentimentAnalysis = null;

    public function __construct()
    {
        $this->dataHandler = new DataHandler();
        $this->requestTwitter = new RequestTwitter();
    }

    public function test(Request $request) {
        return env('UNDEFINED_SHIT');
    }

    public function streaming(Request $request) {
        $origin = $request->input("hashtag");
        if (!$origin) {
            echo json_encode(array(
                'error' => 'No hashtag.'
            ));
            return;
        }
    
        $stream = new StreamingTwitter($origin, 10);
        $stream->consume();
        var_dump($stream->getResults());
    }
    

    public function search(Request $request) {
        $origin = $request->input("hashtag");
        $cnt = $request->input("cnt");
        $streaming = $request->input("streaming") === 'true';
        $result_type = $request->input("result_type");
        $until = $request->input("until");
        $lang = $request->input("lang");
        $geocode = $request->input("geocode");
        $analyse = $request->input("analyse");
        if (!$origin) {
            echo json_encode(array(
                'error' => 'No hashtag.'
            ));
            return;
        }

//        $req = new RequestTwitter();
        $res = $this->requestTwitter->searchHashtag($origin, $cnt, $streaming, $result_type, $until, $lang, $geocode);
        
        $yrpm = $this->dataHandler->parseTwitterResponse($res);
        
        if (($analyse == 'true')) {
            $startAnalysis = $this->analyse($yrpm);
            $yrpm = json_decode($startAnalysis, true);
        }
//        $user = Auth::user();
//        var_dump($user);
        $_SESSION['origin'] = $origin;
        $_SESSION['timestamp'] = time();
        $_SESSION['res'] = $res;
        $_SESSION['yrpm'] = $yrpm;
    
        $tmpYrpm =$this->dataHandler->buildGraph($yrpm);
        $yrpm = $this->dataHandler->averageSentiment($tmpYrpm);
        echo json_encode($yrpm);
    }
    
    public function analyse($tweets) {
        
        $tweets = json_encode($tweets, JSON_UNESCAPED_UNICODE);
        
        $tmpTweetFilePath = public_path()."/tweeter/tempTweet.json";
        $tmpTweet = fopen($tmpTweetFilePath, 'w');
        fwrite($tmpTweet, $tweets);
        fclose($tmpTweet);
        
        $cmd = "python /var/www/html/app/sentiment_analysis.py";
        $output = shell_exec($cmd);
        return $output;
    }

    public function filter(Request $request) {
        $media = $request->input('media');
        $isReady = isset($_SESSION['origin']) && !empty($_SESSION['origin']) &&
            isset($_SESSION['timestamp']) && !empty($_SESSION['timestamp']) &&
            isset($_SESSION['yrpm']) && !empty($_SESSION['yrpm']);
    
        if (!$isReady) {
            return;
        }
    
        $res = $_SESSION['res'];
        $result = new stdClass();
        $result->statuses = array();
    
        if (empty($media)) {
            echo json_encode($this->dataHandler->buildGraph($_SESSION['yrpm']));
        } else {
            if ($media=="Yes") {
                foreach ($res->statuses as $value) {
                    if (isset($value->entities->media)) {
                        $result->statuses[]=$value;
                    }
                }
            } else {
                foreach ($res->statuses as $value) {
                    if (!isset($value->entities->media)) {
                        $result->statuses[]=$value;
                    }
                }
            }
            $yrpm = $this->dataHandler->parseTwitterResponse($result);
            echo json_encode($this->dataHandler->buildGraph($yrpm));
        }
    }

    public function export(Request $request) {
        $isReady = isset($_SESSION['origin']) && !empty($_SESSION['origin']) &&
            isset($_SESSION['timestamp']) && !empty($_SESSION['timestamp']) &&
            isset($_SESSION['yrpm']) && !empty($_SESSION['yrpm']);
    
        if (!$isReady) {
            return;
        }
    
        $this->dataHandler->export($_SESSION['origin'], $_SESSION['timestamp'], $_SESSION['yrpm']);
    
        echo '../public/' . env('TEMP_DIR') . $this->dataHandler->getExportFilename($_SESSION['origin'], $_SESSION['timestamp']);
    }

    public function import(Request $request) {
        $nbFiles = count($_FILES['file']['name']);
        $yrpms = array();
    
        for ($i = 0; $i < $nbFiles; $i++) {
            // Limit the file type to octet-stream (to verify)
            // Limit the file size to 1024KB
            $accepted = $_FILES['file']['type'][$i] === 'application/octet-stream' &&
                $_FILES['file']['size'][$i] <= 1024000;
        
            if (!$accepted) {
                continue;
            }
        
            // Import yrpm directly from temp file on server
            $path = $_FILES['file']['tmp_name'][$i];
            $yrpms[] = $this->dataHandler->import($path);
        
            // Delete temp file on server
            if (file_exists($path)) {
                unlink($path);
            }
        }
    
        $yrpm = $this->dataHandler->mergeImports($yrpms);
        echo json_encode($this->dataHandler->buildGraph($yrpm));
    }

    public function array_console($r = array()) {
        $n = count($r);
        echo "array($n) [<br>";
        foreach ($r as $i => $ele) {
            echo "\t[$i] => ";
            if (is_array($ele)) array_console($ele);
            else {
                echo $ele;
            }
            echo "<br>";
        }
        echo "]<br>";
    }

}
