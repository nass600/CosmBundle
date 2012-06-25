<?php
namespace Nass600\CosmBundle\Model\Manager;

use Nass600\CosmBundle\Model\Manager\ConnectionManager;
use Nass600\CosmBundle\Model\Feed;
use Doctrine\ORM\EntityManager;
use Nass600\CosmBundle\Model\DataTransformer\ResponseToArrayTransformer;

/**
 * FeedManager
 *
 * @package Nass600CosmBundle
 * @subpackage Model
 * @author Ignacio Velazquez <ivelazquez85@gmail.com>
 */
class FeedManager
{
    protected $em;
    protected $connManager;

    /**
     * @param \Nass600\CosmBundle\Model\ConnectionManager $connManager
     */
    public function __construct(ConnectionManager $connManager) {
        $this->connManager = $connManager;
    }

    /**
     * Reads feed
     *
     * @param string $apiVersion
     * @param string $apiKey
     * @param integer $feedId
     *
     * @return array
     */
    public function readFeed($feedId, $apiKey)
    {
        $feed = new Feed();
        $feed->setFeedId($feedId);
        $feed->setApiKey($apiKey);
        $feed->setDatastreams(array(1));
        $feed->setFormat('json');

        $response = ResponseToArrayTransformer::transform($this->connManager->getRequest($feed),
            $feed->getFormat());

        echo "<pre>";
        var_dump($response);
        echo "</pre>";
        die;

//        //if historical query
//        if ($start != null && $end != null) {
//            $startDate = new \DateTime($start);
//            $endDate = new \DateTime($end);
//
//            $pachube = new Pachube($apiVersion, $feedId);
//            $pachube->setStartDate($startDate);
//            $pachube->setEndDate($endDate);
//
//            $url = $pachube->buildUrl();
//
//
//            $response = $this->connManager->_getRequest($url);
//            return $response;
//        }
//        else {
//            $pachube = new Pachube($apiVersion, $feedId);
//
//            //building web service url
//            $url = $pachube->buildUrl();
//
//            //getting data
//            $data = $this->connManager->_getRequest($url);
//
//        }
//
//        return $data;
    }
}
 
