<?php

namespace Nass600\CosmBundle\Model;

/**
 * Feed parameters
 *
 * @package Nass600CosmBundle
 * @subpackage Model
 * @author Ignacio Velazquez <ivelazquez85@gmail.com>
 */
class Feed
{
    protected $format = "json";
    protected $apiKey;
    protected $feedId;
    protected $datastreams;

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
}