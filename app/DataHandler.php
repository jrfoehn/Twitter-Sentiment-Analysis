<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataHandler extends Model
{
    public function parseTwitterResponse($responseTwitter) {
        $yrpm = array();
    
//        if ($responseTwitter != null) {
            foreach ($responseTwitter->statuses as $tweet) {
                if (!isset($tweet->id_str)) {
                    // TODO: log the error!
                    echo "EMPTY";
                    continue;
                }

                // Data structure
                $yrpm[$tweet->id_str] = array(
                    'content' => $tweet->text,
                    'relations' => array(),
                    'sentiment' => array()
                );

                $nbHashtags = count($tweet->entities->hashtags);

                for ($i = 0; $i < $nbHashtags; $i++) {
                    $source = $tweet->entities->hashtags[$i];

                    for ($j = $i + 1; $j < $nbHashtags; $j++) {
                        $target = $tweet->entities->hashtags[$j];

                        $yrpm[$tweet->id_str]['relations'][] = array(
                            'source' => $source->text,
                            'target' => $target->text,
                        );
                    }
                }
            }
//        }
        return $yrpm;
    }

    public function getExportFilename($origin, $timestamp) {
        return str_replace('#', '', $origin) . '-' . $timestamp . '.dat';
    }

    public function export($origin, $timestamp, $yrpm) {
        $filename = self::getExportFilename($origin, $timestamp);
        if (file_exists(env('TEMP_DIR') . $filename)) {
            // Do not overwrite if exportation already called.
            // This avoid to building file content every time exportation is triggered.
            return;
        }

        $nodes = array();

        $filecontent = '';

        // Build metadata
        $filecontent .= 'ORIGIN_HASHTAG' . "\t" . $origin . "\n";
        $filecontent .= 'DATETIME' . "\t\t" . $timestamp . "\n";
        $filecontent .= "\n";

        // Build tweets and relations data
        $tweets = '';
        $relations = '';
        $hashtags = '';
        foreach ($yrpm as $tid => $data) {
            $tweets .= $tid . ' ' . urlencode($data['content']) . "\n";
            foreach ($data['relations'] as $relation) {
                $relations .= $relation['source'] . ' ' . $relation['target'] . ' ' . $tid;
                $relations .= "\n";

                self::buildNode($relation, 'source', $nodes);
                self::buildNode($relation, 'target', $nodes);
            }
        }
        foreach ($nodes as $node) {
            $hashtags .= '#' . $node['data']['label'] . ' ' . $node['data']['weight'];
            $hashtags .= "\n";
        }

        $filecontent .= $tweets . "\n";
        $filecontent .= $relations . "\n";
        $filecontent .= $hashtags . "\n";

        $filecontent .= "END\n";

        file_put_contents(Config::TEMP_DIR . $filename, $filecontent, LOCK_EX);
    }

    public function import($filename) {
        $yrpm = array();
        $origin = '';
        $timestamp = '';

        $filecontent = file_get_contents($filename);
        if (!$filecontent) {
            return false;
        }

        $lines = explode("\n", $filecontent);

        foreach ($lines as $line) {
            $parts = preg_split("/[\s\t]+/", $line);
            if ($parts[0] === '#' || $parts[0] === ';' || $parts[0] === '//') {
                // Ignore comments
                continue;
            } else if ($parts[0] === 'ORIGIN_HASHTAG') {
                $origin = $parts[1];
            } else if ($parts[0] === 'DATETIME') {
                $timestamp = $parts[1];
            } else if (count($parts) == 2) {
                // This is a tweet, parse t_id and its content
                // Note that the content in file was url encoded, so decode it
                $tid = $parts[0];
                $content = urldecode($parts[1]);
                $yrpm[$tid] = array(
                    'content' => $content,
                    'relations' => array(),
                );
            } else if (count($parts) == 3) {
                // This is a relation
                $source = $parts[0];
                $target = $parts[1];
                $tid = $parts[2];
                $yrpm[$tid]['relations'][] = array(
                    'source' => $source,
                    'target' => $target,
                );
            } else {
                // Unknown line content or "END", just ignore
                continue;
            }
        }

        return $yrpm;
    }

    public function mergeImports($yrpms) {
        $result = array();
        for ($i = 0; $i < count($yrpms); $i++) {
            $result = array_merge($result, $yrpms[$i]);
        }
        return $result;
    }

    public function buildGraph($yrpm) {
        $nodes = array();
        $edges = array();
        $tweets = array();
        $hashtagId = 0;
        $tweetSentiment = 0;
        
        foreach ($yrpm as $tweetId => $data) {
            $tweetContent = $data['content'];
            
            if (isset($data['sentiment']) && !is_null($data['sentiment'])) {
                if (isset($data['sentiment']['label']) && !is_null($data['sentiment']['label'])) {
                    if ($data['sentiment']['label'] == 'positive') {
                        $tweetSentiment = 1 * $data['sentiment']['certainty'];
                    }
                    else {
                        $tweetSentiment = -1 * $data['sentiment']['certainty'];
                    }
                }
            }
//            echo "<br>$tweetSentiment";
//            echo "<br>";
            
            foreach ($data['relations'] as $key => $sourceTarget) {
                //build node
                self::buildNode($sourceTarget, 'source', $nodes, $tweetSentiment);
                self::buildNode($sourceTarget, 'target', $nodes, $tweetSentiment);
                
                //build edges
                $rid1 = $sourceTarget['source'] . $sourceTarget['target'];
                $rid2 = $sourceTarget['target'] . $sourceTarget['source'];

                if (isset($edges[$rid1])) {
                    $edges[$rid1]['data']['weight']++;
                    $edges[$rid1]['data']['tweets'][] = $tweetContent;
                } else if (isset($edges[$rid2])) {
                    $edges[$rid2]['data']['weight']++;
                    $edges[$rid2]['data']['tweets'][] = $tweetContent;
                } else {
                    $edges[$rid1] = array(
                        'data' => array(
                            'id' => $rid1,
                            'source' => $sourceTarget['source'],
                            'target' => $sourceTarget['target'],
                            'weight' => 1,
                            'tweets' => array($tweetContent),
                        )
                    );
                }
            }
        }
        return array(
            'nodes' => array_values($nodes),
            'edges' => array_values($edges)
        );
    }

    private function buildNode($relation, $which, &$nodes, $tweetSentiment) { // $which = 'source' or 'target'
        if (!isset($relation[$which])) {
            return;
        }

        if (!isset($nodes[$relation[$which]])) {
            $nodes[$relation[$which]] = array(
                'data' => array(
                    'id' => $relation[$which],
                    'label' => $relation[$which],
                    'weight' => 1,
                    'sentiment' => $tweetSentiment,
                    'color' => '' //for js output
                )
            );
        } else {
            $nodes[$relation[$which]]['data']['weight']++;
            $nodes[$relation[$which]]['data']['sentiment'] += $tweetSentiment;
        }
    }
    
    public function averageSentiment($yrpm) {
        $nodes = $yrpm['nodes'];
        $i = 0;
        foreach ($nodes as $key => $value) {
            $sentiment[$i] = round(($value['data']['sentiment']) / ($value['data']['weight'] * 100), 2);
            $i++;
        }
        for ($i = 0; $i < count($nodes); $i++) {
            $yrpm['nodes'][$i]['data']['sentiment'] = $sentiment[$i];
        }
        return $yrpm;
    }

}
