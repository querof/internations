<?php

namespace App\lib;

use Symfony\Component\HttpFoundation\Response;
use Spatie\ArrayToXml\ArrayToXml;

/*
 * Class thats control and create content negocition
 */

class ContentResponse
{
    /**
    * Return data in JSON or XML format.    *
    *
    * @param Integer $code. HTTP code.
    * @param String $body. Response body or message.
    * @param String $contentType. Type of format/mime (json/xml).
    * @param Integer $id. id of record created, if it is applicable.
    *
    * @return JSON/XML Response.
    */

    public function response($code, $body, $contentType, $id=null)
    {
        $response = new Response();
        $response->headers->set('Content-Type', $this->getMime($contentType));
        $response->setStatusCode($code);
        $response->setContent($this->getContent($body, $contentType, $id));
        return $response;
    }


    /**
    * Return mime type.    *
    *
    * @param String $contentType. Type of format/mime (json/xml).
    *
    * @return String mime type string.
    */

    private function getMime($contentType)
    {
        return  $contentType == 'xml'?'application/xml':'application/json';
    }

    /**
    * Return data in contentType format.    *
    *
    * @param String $body. Response body or message.
    * @param String $contentType. Type of format/mime (json/xml).
    * @param Integer $id. id of record created, if it is applicable.
    *
    * @return JSON/XML Response data.
    */

    private function getContent($body, $contentType, $id)
    {
        return $contentType == 'xml'?ArrayToXml::convert(array('body' => $body)):json_encode(array('body' => $body,'id'=>$id));
    }
}
