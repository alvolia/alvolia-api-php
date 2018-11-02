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
        $ch = $this->initializeCURLHandle(
            $this->endpointURL($endpoint)
        );

        // options for HTTP POST request
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        // fire request
        $response_data = curl_exec($ch);

        // check if any error occured during request
        $err_code = curl_errno($ch);

        // do not forget to free memory
        curl_close ($ch);

        if ($err_code == 0) {
            return json_decode($response_data, false);
        } else {
            return null;
        }
    }

    public function get($endpoint) {
        $ch = $this->initializeCURLHandle(
            $this->endpointURL($endpoint)
        );

        // fire request
        $response_data = curl_exec($ch);

        // check error code
        $err_code = curl_errno($ch);

        // free memory
        curl_close ($ch);

        if ($err_code == 0) {
            return json_decode($response_data, false);
        } else {
            return null;
        }
    }

    protected function defaultHeaders() {
        return array(
            // required to identifie site for API requests
            'X-Site: ' . $this->_clientID,
            // token identifies who is accessing data
            'X-Token: ' . $this->_token,
            // API requires this content type
            "Content-Type: application/x-www-form-urlencoded",
        );
    }

    protected function endpointURL($endpoint) {
        return self::API_ROOT . '/' . self::API_VERSION . '/' . ltrim($endpoint, '/');
    }

    protected function initializeCURLHandle($url) {
        $ch = curl_init();

        // URL is required of any request, so set it at the beginning of initialization
        curl_setopt($ch, CURLOPT_URL, $url);

        // just to be sure we do not block interface for a long time
        // our response should be made within a 25ms, so 1s is more than enough
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        // always wait for reponse from server and return it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // headers are always the same - we need to send auth identifier and token
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->defaultHeaders());

        return $ch;
    }
}