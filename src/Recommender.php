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
                    'item_id' => $itemID,
                    'user_id' => $userID,
                    'recommender_uid' => $recommenderID,
                )
            )
        );
    }

    public function recommenders() {
        return new Recommenders(
            $this->_client->get(
                'recommendations/list'
            )
        );
    }
}

class RecommenderResponse {
    protected $_response;
    public function __construct($recommendationResponse) {
        $this->_response = $recommendationResponse;

        if(!$this->_response) {
            $this->_response = (object) array();
        }

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
        $items = (array) $this->_response->contentbased_items;
        foreach((array) $this->_response->collaborative_items as $k => $v) {
            if(!array_key_exists($k, $items)) {
                $items[$k] = $v;
            } elseif($content[$k] < $v) {
                $items[$k] = $v;
            }
        }
        arsort($items);

        return array_slice($items, 0, $n);
    }
}

class Recommenders {
    protected $_recommenders;

    public function __construct($recommenders) {
        $this->_recommenders = array();
        if($recommenders) {
            foreach((array) $recommenders->recommenders as $k => $v) {
                array_push(
                    $this->_recommenders, (array) $v
                );
            }
        }
    }

    public function get() {
        return $this->_recommenders;
    }
}