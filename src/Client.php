<?php

namespace Alvolia\API;

class Client {
    const API_ROOT = 'https://api.adboost.sk';
    const API_VERSION = 'v201804';

    protected $_clientID;
    protected  $_token;

    public function __construct($clientID, $token) {
        $this->_clientID = $clientID;
        $this->_token = $token;
    }

    public function recommender() {
        return new Recommender($this);
    }

    public function post($endpoint, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpointURL($endpoint));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->defaultHeaders());

        $response_data = curl_exec($ch);
        $err_code = curl_errno($ch);
        curl_close ($ch);
        if ($err_code == 0) {
            return json_decode($response_data, false);
        } else {
            return null;
        }
    }

    public function get($endpoint) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endpointURL($endpoint));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->defaultHeaders());

        $response_data = curl_exec($ch);
        $err_code = curl_errno($ch);
        curl_close ($ch);
        if ($err_code == 0) {
            return json_decode($response_data, false);
        } else {
            return null;
        }
    }

    protected function defaultHeaders() {
        return array(
            'X-Site: ' . $this->_clientID,
            'X-Token: ' . $this->_token,
            "Content-Type: application/x-www-form-urlencoded",
        );
    }

    protected function endpointURL($endpoint) {
        return self::API_ROOT . '/' . self::API_VERSION . '/' . ltrim($endpoint, '/');
    }
}