<?php
namespace Application\Models\Gateways\Parser;

use Application\Models\Entities\SAPErpOrganisation;
use Application\Models\Gateways\SAXParserGateway;

/**
 * Extends SAX parser gateway class
 */
class SAPOrganisationParser extends SAXParserGateway
{
    /**
     * Implemented function to setup task when a xml tag starts
     * @param $parser
     * @param sgtring $tag_name
     * @param string $attributes
     * @return void
     */
    public function startTag($parser, $tag_name, $attributes)
    {
        if ($tag_name == 'OrgUnit') {
            $this->entity = new SAPErpOrganisation();
            $this->started = true;
        } else {
            $this->currentTag = $tag_name;
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
        if ($tag_name == 'OrgUnit') {
            $this->entity->save();
        }
    }

    /**
     * Implemented function to work with tag data once it is initiated in startTag() funciton
     * @param $parser
     * @param mixed $data
     * @return void
     */
    public function tagData($parser, $data)
    {
        if ($this->currentTag != '' && $this->started) {
            $this->entity->{$this->currentTag} = $data;
        }
        $this->currentTag = '';
    }
}
