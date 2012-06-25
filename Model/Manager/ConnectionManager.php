<?php

namespace Nass600\CosmBundle\Model\Manager;

use \Exception;
use Nass600\CosmBundle\Model\Feed;

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

    protected $header;
    protected $baseUrl = "http://api.cosm.com/v2";

    /**
     * Builds the web service url
     *
     * @param Feed $feed
     *
     * @return string
     * @throws Exception
     */
    public function buildUrl(Feed $feed)
    {
        if (null == $feed->getApiKey() || null == $feed->getFeedId()) {
            throw new Exception("You must set the feed Id and API key", 500);
        }

        $this->setHeader(array("X-PachubeApiKey: {$feed->getApiKey()}"));

        $url = implode('/', array($this->baseUrl, "feeds", "{$feed->getFeedId()}.{$feed->getFormat()}"));

        // If datastreams set
        if (count($feed->getDatastreams()) > 0) {
            $datastreams = implode(',', $feed->getDatastreams());

            return "$url?datastreams={$datastreams}";
        }

        return $url;
    }

    /**
     * Get headers
     * 
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set headers
     *
     * @param array $headers
     */
    protected function setHeader(array $headers)
    {
        $this->header = $headers;
    }

    /**
     * Create GET request to Cosm for retrieving a feed
     *
     * @param Feed $feed
     *
     * @return response
     * @throw Exception
     */
    public function getRequest(Feed $feed)
    {
        try {
            $url = $this->buildUrl($feed);
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
            $this->exceptionHandler(self::MISSING_CURL);
    }

    /**
     * Create GET request to Cosm for creating a feed
     *
     * @param Feed $feed
     * @param $eeml
     *
     * @return response
     * @throw Exception
     */
    public function createRequest(Feed $feed, $eeml)
    {
        try {
            $url = $this->buildUrl($feed);
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
            $this->exceptionHandler(self::MISSING_CURL);
    }

    /**
     * Create PUT request to Cosm for adding new feed
     *
     * @param Feed $feed
     * @param string data
     *
     * @return response
     */
    public function putRequest(Feed $feed, $data)
    {
        try {
            $url = $this->buildUrl($feed);
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

            $response = $this->curl($options);
            fclose($putData);

            return $response['status']['http_code'];
        }
        elseif (function_exists('file_put_contents') && ini_get('allow_url_fopen'))
            return $this->put($url, $data);
        else
            $this->exceptionHandler(self::MISSING_CURL);
    }

    /**
     * Create DELETE request to Cosm for removing a feed
     *
     * @param Feed $feed
     *
     * @return response
     */
    public function deleteRequest(Feed $feed)
    {
        try {
            $url = $this->buildUrl($feed);
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
            $this->exceptionHandler(self::MISSING_CURL);
    }

    /**
     * cURL main function
     *
     * @param array $options
     *
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
 
