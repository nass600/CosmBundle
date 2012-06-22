<?php
namespace Nass600\CosmBundle\Model;

use Nass600\CosmBundle\Model\ConnectionManager;
use Doctrine\ORM\EntityManager;
use Ideup\PachubeBundle\Entity\Pachube;
use Ideup\PachubeBundle\Formatter\Formatter;

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
     */
    public function readFeed($feedId, $apiKey)
    {
        $this->connManager->setFeedId($feedId);
        $this->connManager->setApiKey($apiKey);
        $this->connManager->setDatastreams(array(1));

        $response = json_decode($this->connManager->getRequest());

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
 
