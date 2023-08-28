<?php

namespace Src;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;

class HttpClient {

    protected $client;
    protected $use_cookie = false;
    protected $cookie_file;
    protected $options = [];

    /**
     * Class constructor : creating new Client() and cookies file
     */
    function __construct($cookies = true, $verify = true) {
        if($cookies) {
            $this->use_cookie   = true;
            $this->cookie_file  = tempnam('/tmp/', 'curl-cookie');

            //Setting cookies file
            $this->setCookies();
        }
        $this->client = new Client(['verify' => $verify]);
    }

    /**
     * Class destructor : unlink cookie file
     */
    function __destruct() {
        if($this->use_cookie) {
            unlink($this->cookie_file);
        }
    }

    /**
     * Set HTTP headers in the request options
     *
     * @param array $heades => ['Content-Type'  => 'application/json']
     * @return null
     */
    public function addHeaders(array $headers = []) {
        if(!empty($headers)) {
            $this->options['headers'] = [];
            foreach($headers as $key => $value) {
                $this->options['headers'][$key] = $value;
            }
        }
    }

    /**
     * Delete all HTTP headers
     *
     * @return null
     */
    public function hideHeaders() {
        $this->options['headers'] = false;
    }

    /**
     * Set HTTP request body
     *
     * @param array $params => [ 'query' => ['key' => 'value'] | 'body/other' => [...] ]
     * @param bool $needEncode : request needs a json body
     * @return null
     */
    public function setBody($params, $needEncode = true) {
        foreach( $params as $key => $body ) {
            if($needEncode && !in_array($key, ['query', 'form_params']) && is_array($body)) {
                $this->options[$key] = trim(json_encode($body));
            } else {
                $this->options[$key] = $body;
            }
        }
    }

    /**
     * Create new cookies file : used to hold back the logged session
     *
     * @return null
     */
    public function setCookies() {
        $this->options['cookies'] = new FileCookieJar($this->cookie_file);
    }

    /**
     * Set HTTP timeout in seconds
     *
     * @param int $connection_timeout
     * @param int $timeout
     * @return null
     */
    public function setTimeout($connection_timeout = 15, $timeout = 15) {
        $this->options['connect_timeout']   = $connection_timeout;
        $this->options['timeout']           = $timeout;
    }

    /**
     * Sets custom option to request
     *
     * @param string $key
     * @param mixed $value
     */
    public function setOption($key, $value) {
       $this->options[$key] = $value;
    }

    /**
     * Delete option "key" from options array
     *
     * @param string $key
     */
    public function deleteOption($key) {
        if(isset($this->options[$key])) {
            unset($this->options[$key]);
        }
    }

    /**
     * Checks if http method is valid
     *
     * @param string $method
     * @return bool $response
     */
    private function _isValidHttpMethod($method) {
        return in_array($method, ['GET', 'POST', 'PUT', 'DELETE']);
    }

    /**
     * Make HTTP request
     *
     * @param string $method => GET | POST | PUT | DELETE
     * @param string $uri : request endpoint
     * @param array $params
     * @param bool $encode_params => specify if $params needs to be encoded
     * @return mixed $response
     */
    public function makeRequest($method = 'GET', $uri, $params = [], $encode_params = true) {
        if(!$this->_isValidHttpMethod($method)) {
            return false;
        }

        //Encoding request body
        if(!empty($params)) {
            $this->setBody($params, $encode_params);
        }

        //Making request
        $response = $this->client->request($method, $uri, $this->options);

        try {
            //Return object response
			return json_decode($response->getBody()->getContents());

		} catch(Exception $e) {
			//Return false : the exception is non-blocking
			return false;
		}
    }

    /**
     * Makes HTTP request and returns HTML page response
     *
     * @param string $method => GET | POST | PUT | DELETE
     * @param string $uri : request endpoint
     * @param array $params
     * @param bool $encode_params => specify if $params needs to be encoded
     * @param bool $return_only_content => $response is only the result of getContent() function
     * @return array|string $response => ['code' => HTTP_RESPONSE_CODE, 'html' => '<html></html>']
     */
    public function makeHtmlRequest($method = 'GET', $uri, $params = [], $encode_params = true, $return_only_content = false) {
        if(!$this->_isValidHttpMethod($method)) {
            return false;
        }

        //Encoding request body
        if(!empty($params)) {
            $this->setBody($params, $encode_params);
        }

        try {
            //Making request
            $response = $this->client->request($method, $uri, $this->options);

            //Return only content from $response
            if($return_only_content) {
                return $response->getBody()->getContents();
            }

			//Return HTML response content and HTTP code
            return [
                'code' => $response->getStatusCode(),
                'html' => $response->getBody()->getContents()
            ];

		} catch(Exception $e) {
			//Return false : the exception is non-blocking
			return false;
		}
    }
}
