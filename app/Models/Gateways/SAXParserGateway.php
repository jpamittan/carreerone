<?php
namespace Application\Models\Gateways;

/**
 * Abstract class for SAX (Simple API for XML) parser gateway
 */
abstract class SAXParserGateway
{
    protected $response = array();
    protected $records = 0;
    protected $started = false;
    protected $entity;
    protected $currentTag;

    abstract public function startTag($parser, $tag_name, $attributes);
    abstract public function endTag($parser, $tag_name);
    abstract public function tagData($parser, $data);

    /**
     * Function to return response
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Function to return records processed
     * @return integer
     */
    public function getRecords()
    {
        return $this->records;
    }
}
