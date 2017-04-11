<?php
//
//namespace App;
//
//use Illuminate\Database\Eloquent\Model;
//
//class HashtagSentimentAnalysis extends Model
//{
//
////    public function analysis($responseTwitter) {
////
////        $dictionary = $this->loadClassifier();
////
////        foreach ($responseTwitter->statuses as $tweet) {
////
////            if (!isset($tweet->id_str)) {
////
////                echo "EMPTY";
////                continue;
////
////            }
////
////            // Data structure
////            $singleTweet[$tweet->id_str]['text'] = $this->preprocess($tweet->text);
////            $token = $this->singleTweetTokenization($singleTweet[$tweet->id_str]['text']);
////            $tokenizedTweet[$tweet->id_str] = array_values($token);
////
////        }
////    }
//
//    public function analyse($responseTwitter) { //label_for_tweet
//        $tokenizedTweet = $this->tokenization($responseTwitter);
//        $classifier = $this->loadClassifier();
//        $feature = $this->loadFeature();
//        $casePositive = $feature["p(+)"];
//        $caseNegative = $feature["p(-)"];
//        $prob_null_pos = 10000*(1/floatval($feature["positive_tokens"] + $feature["total_tokens"]));
//        $prob_null_neg = 10000*(1/floatval($feature["negative_tokens"] + $feature["total_tokens"]));
//
//        foreach ($tokenizedTweet as $key=>$token) {
//            foreach ($token as $key=>$t) {
//                foreach ($t as $item) {
//                    if (key_exists($item, $classifier)) {
////                        echo "<br>";
////                        echo $item;
////                        echo "<br>";
////                        print_r($classifier[$item]);
////                        $tokenDic[$item] = $classifier[$t];
//                        $casePositive *= 10000*$classifier[$item]["p(+)"];
//                        $caseNegative *= 10000*$classifier[$item]["p(-)"];
//                    }
//                    else {
//                        $casePositive *= $prob_null_pos;
//                        $caseNegative *= $prob_null_neg;
//                    }
//
//                }
//            }
////            $result = $casePositive - $caseNegative;
////            if ($result >= 0) {
////                $label = "pos";
////            }
////            elseif ($result < 0) {
////                $label = "neg";
////            }
////
////            $resultMax = max($casePositive, $caseNegative);
////            $resultMin = min($casePositive, $caseNegative);
////            $r = 1 - $resultMin/floatval($resultMax);
////            $ratio = round($r, 2);
////
////            echo $ratio;
//        }
//        //TODO return label and ratio, check with Arnaud
//    }
//
//    public function tokenization($responseTwitter) {
//
//        $singleTweet = array();
//        $tokenizedTweet = array();
//
//        foreach ($responseTwitter->statuses as $tweet) {
//
//            if (!isset($tweet->id_str)) {
//
//                echo "EMPTY";
//                continue;
//
//            }
//
//            // Data structure
//            $singleTweet[$tweet->id_str]['text'] = $this->preprocess($tweet->text);
//            $token = $this->singleTweetTokenization($singleTweet[$tweet->id_str]['text']);
//            $tokenizedTweet[$tweet->id_str] = array_values($token);
//
//        }
//
//        return $tokenizedTweet;
//    }
//
//    public function singleTweetTokenization($tweet) {
//
//        $token = explode(' ', $tweet);
//        $twoConsecutive = array();
//        $twoAhead = array();
//
//        for ($i = 0; $i < (count($token) - 1); $i++) {
//            if (array_key_exists($i+1, $token)) {
//                array_push($twoConsecutive, $token[$i].$token[$i+1]);
//            }
//            if (array_key_exists($i+2, $token)) {
//                array_push($twoAhead, $token[$i].$token[$i+2]);
//            }
//        }
//
////        return array($token, $twoConsecutive, $twoAhead);
////        $token = $token.$twoConsecutive);
//
//        $token = array_merge(array_values($token), array_values($twoConsecutive), array_values($twoAhead));
////        print_r($token);
//        return array($token);
//    }
//
//    public function preprocess($tweetText) {
//
//        $haystack = strtolower($tweetText);
//
//        //replace @user
//        $atUserRegex = '/@([\w-]+)/i';
//        $haystack = preg_replace($atUserRegex, 'AT_USER', $haystack);
//
//        //replace all url
//        $urlRegex = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
//        $haystack = preg_replace($urlRegex, 'URL', $haystack);
//
//        //replace dots and commas
//        $haystack = str_replace(array('.', ',', ';', ':'), '' , $haystack);
//
//        return $haystack;
//    }
//
//    public function loadClassifier() {
//        $classifierPath = storage_path()."/app/classifier/classifier_global.json";
//        if (!file_exists($classifierPath)){
//            echo "Invalid File";
//        }
//        else {
//            $file = file_get_contents($classifierPath);
//            $classifier = json_decode($file, true);
//            return $classifier;
//        }
//    }
//
//    public function loadFeature() {
//        $featurePath = storage_path()."/app/classifier/features_global.json";
//        if (!file_exists($featurePath)){
//            echo "Invalid File";
//        }
//        else {
//            $file = file_get_contents($featurePath);
//            $feature = json_decode($file, true);
//            return $feature;
//        }
//    }
//
//}
