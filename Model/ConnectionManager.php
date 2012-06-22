<?php

namespace Nass600\CosmBundle\Model;

/**
 * Manages REST connection
 *
 * @package Nass600CosmBundle
 * @subpackage Model
 * @author Ignacio Velazquez <ivelazquez85@gmail.com>
 */
class ConnectionManager
{
    const WRONG_API_KEY = 401;
    const WRONG_API_VERSION = 402;
    const MISSING_PARAMS = 418;
    const MISSING_CURL = 500;

    protected $format = "json";
    protected $apiKey;
    protected $feedId;
    protected $header;
    protected $baseUrl = "http://api.cosm.com/v2";
    protected $datastreams;

    /**
     * Builds the web service url
     *
     * @return string
     * @throws Exception
     */
    public function buildUrl()
    {
        if (null == $this->apiKey || null == $this->feedId) {
            throw Exception("You must set the feed Id and API key", 500);
        }

        // If datastreams set
        if (count($this->datastreams) > 0) {
            $datastreams = implode(',', $this->datastreams);
            return implode('/', array(
                $this->baseUrl,
                "feeds",
                $this->getFeedId() . ".$this->format?datastreams={$datastreams}"
            ));
        }

        return implode('/', array($this->baseUrl, "feeds", $this->getFeedId()));
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getDatastreams()
    {
        return $this->datastreams;
    }

    public function setDatastreams(array $datastreams)
    {
        $this->datastreams = $datastreams;
    }

    /**
     * get headers
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * set headers
     *
     * @param string $headers
     */
    protected function setHeader(array $headers)
    {
        $this->header = $headers;
    }

    /**
     * get apiKey
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * set apiKey
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->setHeader(array("X-PachubeApiKey: $apiKey"));
    }

    /**
     * get feedId
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * set feedId
     *
     * @param string $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * Create GET request to Cosm for retrieving a feed
     *
     * @return response
     * @throw Exception
     */
    public function getRequest()
    {
        try {
            $url = $this->buildUrl();
        }
        catch(Exception $e) {
            throw $e;
        }

        if (function_exists('curl_init')) {
            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER     => $this->getHeader(),
            );

            $response = $this->curl($options);
            $status = $response['status']['http_code'];

            if ($status == 200) {
                return $response['data'];
            }
            else {
                $this->exceptionHandler($status);
            }
        }
        elseif (function_exists('file_get_contents') && ini_get('allow_url_fopen'))
            return $this->get($url);
        else
            $this->exceptionHandler(Connection::MISSING_CURL);
    }

    /**
     * Create GET request to Cosm for creating a feed
     *
     * @return response
     * @throw Exception
     */
    public function createRequest()
    {
        try {
            $url = $this->buildUrl();
        }
        catch(Exception $e) {
            throw $e;
        }

        if (function_exists('curl_init')) {
            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER     => $this->getHeader(),
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_POST           => true,
                CURLOPT_HEADER         => true,
                CURLOPT_POSTFIELDS     => $eeml,
            );

            $response = $this->curl($options);

            return $response['status']['http_code'];
        }
        else
            $this->exceptionHandler(Connection::MISSING_CURL);
    }

    /**
     * Create PUT request to Cosm for adding new feed
     *
     * @param string url
     * @param string data
     * @return response
     */
    public function putRequest($data)
    {
        try {
            $url = $this->buildUrl();
        }
        catch(Exception $e) {
            throw $e;
        }

        if (function_exists('curl_init')) {
            $putData = tmpfile();
            fwrite($putData, $data);
            fseek($putData, 0);

            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER     => $this->getHeader(),
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_INFILE         => $putData,
                CURLOPT_INFILESIZE     => strlen($data),
                CURLOPT_PUT            => true,
            );

            $response = $this->curlPut($options);
            fclose($putData);

            return $response['status']['http_code'];
        }
        elseif (function_exists('file_put_contents') && ini_get('allow_url_fopen'))
            return $this->put($url, $data);
        else
            $this->exceptionHandler(Connection::MISSING_CURL);
    }

    /**
     * Create DELETE request to Cosm for removing a feed
     *
     * @param string url
     * @param string data
     * @return response
     */
    public function deleteRequest()
    {
        try {
            $url = $this->buildUrl();
        }
        catch(Exception $e) {
            throw $e;
        }

        if (function_exists('curl_init')) {
            $options = array(
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_HTTPHEADER     => $this->getHeader(),
                CURLOPT_CUSTOMREQUEST  => "DELETE",
            );

            $response = $this->curl($options);

            return $response['status']['http_code'];
        }
        else
            $this->exceptionHandler(Connection::MISSING_CURL);
    }

    /**
     * cURL main function
     *
     * @param string url
     * @param bool authentication
     * @return response
     */
    private function curl(array $options)
    {
        $ch = curl_init();

        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);

        $headers = curl_getinfo($ch);
        curl_close($ch);

        return array(
            'data'      => $data,
            'status'    => $headers,
        );
    }

    /**
     * GET requests to Cosm
     *
     * @param string url
     * @return response
     */
    private function get($url)
    {
        // Create a stream
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => $this->getHeader(),
            )
        );
        //        $opts['http']['method'] = "GET";
        //        $opts['http']['header'] = "X-PachubeApiKey: " . $this->apiKey . "\r\n";
        $context = stream_context_create($options);

        // Open the file using the HTTP headers set above
        return file_get_contents($url, false, $context);
    }

    /**
     * PUT requests to Cosm
     *
     * @param string url
     * @param string data
     * @return response
     */
    private function put($url, $data)
    {
        // Create a stream
        $options = array(
            'http' => array(
                'method'  => 'PUT',
                'header'  => $this->getHeader(),
                'content' => $data
            )
        );
        //        $opts['http']['method'] = "PUT";
        //        $opts['http']['header'] = "X-PachubeApiKey: " . $this->apiKey . "\r\n";
        //        $opts['http']['header'] .= "Content-Length: " . strlen($data) . "\r\n";
        //        $opts['http']['content'] = $data;
        $context = stream_context_create($options);

        // Open the file using the HTTP headers set above
        return file_get_contents($url, false, $context);
    }

    /**
     * Handles object Exceptions
     *
     * @param $statusCode
     * @throws \Exception
     */
    public function exceptionHandler($statusCode)
    {
        switch ($statusCode)
        {
            case 200:
                $msg = "Pachube feed successfully updated";
                break;
            case $this::WRONG_API_KEY:
                $msg = "Pachube API key was incorrect";
                break;
            case $this::WRONG_API_VERSION:
                $msg = "Pachube API version not supported";
                break;
            case 404:
                $msg = "Feed ID or some other parameter does not exist";
                break;
            case 422:
                $msg = "Unprocessable Entity, semantic errors (CSV instead of XML?)";
                break;
            case $this::MISSING_PARAMS:
                $msg = "Error in feed ID, data type or some other data";
                break;
            case $this::MISSING_CURL:
                $msg = "cURL library not installed or some other internal error occured";
                break;
            default:
                $msg = "Status code not recognised: " . $statusCode;
                break;
        }
        throw new \Exception($msg);
    }
}
 
