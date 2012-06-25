<?php

namespace Nass600\CosmBundle\Model\DataTransformer;

/**
 * RequestToArrayTransformer
 *
 * @package Nass600CosmBundle
 * @subpackage Model
 * @author Ignacio Velazquez <ivelazquez85@gmail.com>
 */
class ResponseToArrayTransformer
{
    /**
     * Transforms the API response into a PHP array
     *
     * @static
     * @param $response
     * @param $format
     * @return array|mixed|DOMDocument
     */
    public static function transform($response, $format)
    {
        if ($format == "json") {
            return json_decode($response);
        }
        elseif ($format == "xml") {
            return XML2Array::createArray($response);
        }
        else {
            throw new \Exception("$format transformer is not implemented yet");
        }
    }
}