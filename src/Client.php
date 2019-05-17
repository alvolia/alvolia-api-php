<?php

namespace Alvolia\API;

class Client {
    // points to domain where API seats
    const API_ROOT = 'https://api.adboost.sk';

    // API_VERSION points always to latest version of API
    // if you want to use older version, please specify it in constructor of client
    const API_VERSION = 'v201902';

    protected $_clientID;
    protected $_token;
    protected $_apiVersion;
    protected $_apiRoot;

    public function __construct($clientID, $token, $apiRoot = null, $apiVersion = null) {
        $this->_clientID = $clientID;
        $this->_token = $token;
        $this->_apiVersion = $apiVersion != null ? $apiVersion : self::API_VERSION;
        $this->_apiRoot = $apiRoot != null ? $apiRoot : self::API_ROOT;
    }

    public function recommender() {
        return new Recommender($this);
    }

    public function post($endpoint, $data) {
        $encoded_data = json_encode($data);

        $ch = $this->initializeCURLHandle(
            $this->endpointURL($endpoint),
            strlen($encoded_data)
        );

        // options for HTTP POST request
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);

        // fire request
        $response_data = curl_exec($ch);

        // check if any error occured during request
        $err_code = curl_errno($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // do not forget to free memory
        curl_close ($ch);

        if ($err_code == 0 && $httpcode < 300) {
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

    protected function defaultHeaders($contentLength) {
        $headers = array(
            // required to identifie site for API requests
            'X-Site: ' . $this->_clientID,
            // token identifies who is accessing data
            'X-Token: ' . $this->_token,
            // API requires this content type
            "Content-Type: application/json",
        );

        if($contentLength != 0) {
            $headers['Content-Length'] = $contentLength;
        }

        return $headers;
    }

    protected function endpointURL($endpoint) {
        return $this->_apiRoot . '/' . $this->_apiVersion . '/' . ltrim($endpoint, '/');
    }

    protected function initializeCURLHandle($url, $contentLength) {
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->defaultHeaders($contentLength));

        return $ch;
    }
}