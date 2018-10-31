<?php

namespace Alvolia\API;

class Recommender {
    protected $_client;

    public function __construct($client) {
        $this->_client = $client;
    }

    public function recommend($userID, $itemID, $recommenderID) {
        return new RecommenderResponse(
            $this->_client->post(
                'recommendations/recommend',
                array(
                    'product_id' => $itemID,
                    'user_id' => $userID,
                    'recommender_uid' => $recommenderID,
                )
            )
        );
    }
}

class RecommenderResponse {
    protected $_response;
    public function __construct($recommendationResponse) {
        $this->_response = $recommendationResponse;
        if(!property_exists($this->_response, 'contentbased_items')) {
            $this->_response->contentbased_items = array();
        }

        if(!property_exists($this->_response, 'collaborative_items')) {
            $this->_response->collaborative_items = array();
        }
    }

    public function contentBasedItems($n = 5) {
        $items = (array) $this->_response->contentbased_items;
        arsort($items);
        return array_slice($items, 0, $n);
    }

    public function collaborativeItems($n = 5) {
        $items = (array) $this->_response->collaborative_items;
        arsort($items);
        return array_slice($items, 0, $n);
    }

    public function mostSimilarItems($n = 5) {
        $content = (array) $this->_response->contentbased_items;
        foreach((array) $this->_response->collaborative_items as $k => $v) {
            if(!array_key_exists($k, $content)) {
                $content[$k] = $v;
            } elseif($content[$k] < $v) {
                $content[$k] = $v;
            }
        }
    }
}