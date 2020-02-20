<?php
namespace Application\Models\Gateways\Parser;

use Application\Models\Gateways\SAXParserGateway;

/**
 * Extends SAX parser gateway class
 */
class BGWResponseParser extends SAXParserGateway
{
    protected $openedTag = array();
    protected $info = false;
    protected $error = false;
    protected $fault = false;

    public function __construct()
    {
        $this->response = array(
            'status' => '',
            'info' => array(),
            'error' => array(),
            'error_levels' => array(),
            'resume_id' => '',
            'seeker_id' => '',
        );
    }

    /**
     * Implemented function to setup task when a xml tag starts
     * @param $parser
     * @param sgtring $tag_name
     * @param string $attributes
     * @return void
     */
    public function startTag($parser, $tag_name, $attributes)
    {
        $this->openedTag[$tag_name] = $tag_name;
        if ($tag_name == 'faultstring') {
            $this->response['status'] = 'failure';
            $this->response['error_levels']['fault'] = 'fault';
            $this->fault = true;
        }
        if ($tag_name == 'ReturnCode' && isset($attributes['returnCodeType']) && $attributes['returnCodeType'] == 'failure') {
            $lineage = array_reverse($this->openedTag);
            $lineage = array_keys($lineage);
            $this->response['status'] = 'failure';
            $this->response['error_levels'][$lineage[2]] = $lineage[2];
        }
        if (isset($attributes['descriptionType']) && $attributes['descriptionType'] == 'info') {
            $this->info = true;
        }
        if (isset($attributes['descriptionType']) && $attributes['descriptionType'] == 'error') {
            $this->error = true;
        }
        if (isset($attributes['textResumeId'])) {
            $this->response['resume_id'] = array_get($attributes, 'textResumeId', 0);
        }
        if (isset($attributes['userId'])) {
            $this->response['seeker_id'] = array_get($attributes, 'userId', 0);
        }

    }

    /**
     * Implemented function to set up task when a xml tag ends
     * @param $parser
     * @param string @tag_name
     * @return void
     */
    public function endTag($parser, $tag_name)
    {
        $this->fault = false;
        $this->info = false;
        $this->error = false;
        unset($this->openedTag[$tag_name]);
    }

    /**
     * Implemented function to work with tag data once it is initiated in startTag() funciton
     * @param $parser
     * @param mixed $data
     * @return void
     */
    public function tagData($parser, $data)
    {
        if ($this->fault || $this->error) {
            $this->response['error'][] = $data;
        }
        if ($this->info) {
            $this->response['info'][] = $data;
        }
    }
}
