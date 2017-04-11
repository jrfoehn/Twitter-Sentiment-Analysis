#!/usr/bin/env python
# -*- coding: utf-8 -*-
import sys, os, json, nltk, re
from collections import Counter
reload(sys)
sys.setdefaultencoding('utf-8')

IS_POSSIBLY_UNDETERMINED = True
CERTAINTY_RATE = 0.3


class Tweet():
    tokens = [] # List of all the tokens
    text = ''

    def __init__(self, rawtweet):
        self.tokens = []
        self.text = ""
        self.preprocess(rawtweet)
        self.extract_features()

    def preprocess(self, rawtweet):
        try:
            rawtweet = rawtweet.lower()
            rawtweet =  re.sub('\\n','', rawtweet) #gets rid of line breaks
            rawtweet =  re.sub('@\S*','AT_USER', rawtweet) #banalizes user references
            rawtweet =  re.sub('https?://\S*', 'URL ', rawtweet)
            rawtweet =  re.sub('www\S*', 'URL ', rawtweet) #banalizes links
            # self.text = ' \u'.join(tweet.split('\\u')) # attempt to treat emojis
            rawtweet =  re.sub("[/@'\\$`,\-#%&;.:=[{}()$0.""]", '', rawtweet) 
            self.text = rawtweet
        except Exception as e:
            exc_type, exc_obj, exc_tb = sys.exc_info()
            fname = os.path.split(exc_tb.tb_frame.f_code.co_filename)[1]
            # print(exc_type, fname, exc_tb.tb_lineno)


    def extract_features(self):

        tokens = [word for word in nltk.word_tokenize(self.text.decode('utf-8'))]

        n_grams = []
        dict_features = {}

        try:
            for t in tokens:
                n_grams.append(t)

            for t in range(len(tokens)-1): # Consecutive words
                n_grams.append('+'.join(sorted([tokens[t],tokens[t+1]]))) # Adds consecutive bigrams to n_grams


            for t in range(len(tokens)-2): # Two ahead
                n_grams.append('+'.join(sorted([tokens[t], tokens[t+2]])))

        except Exception as e:
            exc_type, exc_obj, exc_tb = sys.exc_info()
            fname = os.path.split(exc_tb.tb_frame.f_code.co_filename)[1]
            print(exc_type, fname, exc_tb.tb_lineno)
            n_grams = []
        self.tokens = n_grams

    def __del__(self):
        self.label = ''
        self.tokens = []
        self.text = ''

class Classifier():

    global_dict = {}
    features = {}
    features_filename = ''
    classifier_filename = ''

    def __init__(self, **keyword_parameters):

        self.import_global_dict()

# Imports the previous information, or creates blank files and variables
    def import_global_dict(self):
        self.features_filename = FEATURES_FILE
        self.classifier_filename = CLASSIFIER_FILE

        # Classifier file
        if not os.path.isfile(self.classifier_filename):
            f = open(self.classifier_filename, 'w').close()
        with open(self.classifier_filename, 'r') as f:
            p = f.read()
            if f:
                try:
                    self.global_dict = Counter(json.loads(p))
                except Exception as e:
                    self.global_dict = Counter(dict())
            f.close()

        # Insights file
        if not os.path.isfile(self.features_filename):
            f = open(self.features_filename, 'w').close()
        with open(self.features_filename, 'r') as f:
            p = f.read()
            if f:
                try:
                    self.features = json.loads(p)
                except:
                    self.features = dict()
            f.close()

    def make_labels(self, tweets):
    	self.global_dict = dict(self.global_dict)
        for k in tweets:
            t = Tweet(tweets[k]['content'])
            if len(t.tokens):
                output = self.label_prevision_for_tweet(t.tokens)
            if output:
                # print output
                label = output['label']
                ratio = output['ratio']

            tweets[k]['sentiment'] = {'label' : label, 'certainty' : ratio}

        return tweets

    def label_prevision_for_tweet(self, tokens):
        try:
            case_positive = self.features['p(+)']
            case_negative = self.features['p(-)']
            prob_null_pos = 1000000*(1/ float((self.features['positive_tokens'] + self.features['total_tokens'])))
            prob_null_neg = 1000000*(1/ float((self.features['negative_tokens'] + self.features['total_tokens'])))

            tokens_dict = {} # Local dict to store the tweet's tokens

            for t in tokens: 
                try: #If tokens exist in global_dict
                    tokens_dict[t] = self.global_dict[t]
                    case_positive *= 1000000*tokens_dict[t]['p(+)']
                    case_negative *= 1000000*tokens_dict[t]['p(-)']
                
                except Exception as e: # Consider existence in dict as 0
                    case_positive *= prob_null_pos
                    case_negative *= prob_null_neg

            result = case_positive - case_negative
            # print result, prob_null_pos, prob_null_neg, case_negative, case_positive
            if result >= 0:
                label = 'positive'
            elif result < 0:
                label = 'negative'

            res_max = max(case_positive, case_negative)
            res_min = min(case_positive, case_negative)
            r = 1- res_min/float(res_max)
            ratio = '{:.2%}'.format(r)

            if (IS_POSSIBLY_UNDETERMINED and (r < CERTAINTY_RATE)):
                label = 'undetermined'

        except Exception as e:
            exc_type, exc_obj, exc_tb = sys.exc_info()
            fname = os.path.split(exc_tb.tb_frame.f_code.co_filename)[1]
            print(exc_type, fname, exc_tb.tb_lineno)
            label = 'undetermined'
            ratio = 0


        results = {'label': label,'ratio': ratio}
        return results


if __name__ == '__main__':

    CLASSIFIER_FILE = '/var/www/html/resources/tweeter/classifier/classifier_global.json'
    FEATURES_FILE = '/var/www/html/resources/tweeter/classifier/features_global.json'
    TWEET_FILE = '/var/www/html/public/tweeter/tempTweet.json'
    with open(TWEET_FILE) as data_file:
        tweets = json.load(data_file)

    d = Classifier()

    labelled_tweets = d.make_labels(tweets)
    print json.dumps(labelled_tweets)
