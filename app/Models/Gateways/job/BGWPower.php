<?php
namespace Application\Models\Gateways\Job;

use Application\Models\Gateways\Http\SoapRequest;
use Application\Models\Gateways\Job\BGWAbstract;
use Application\Models\Gateways\Parser\BGWResponseParser;
use View;

class BGWPower extends BGWAbstract
{
    /**
     * Construct
     */
    public function __construct($credentials)
    {
        $this->credentials = $credentials;
        $this->context = 'BGWPower';
    }

    /**
     * Function to post a resume to Monster API
     * @param string @text_resume
     * @return integer
     */
    public function postResumeToBoard($profile_do)
    {
        $data = array();
        $timestampBGW = date(DATE_W3C);
        $data['timestampBGW'] = $timestampBGW;
        $data['credentials'] = $this->credentials;
        $data['text_resume'] = $profile_do->text_resume;
        $data['xcode'] = $profile_do->xcode;

        $view = View::make('api.bgw.power-resume', array(
            'data' => $data,
        ));
        $template = $view->render();

        $client = new SoapRequest();
        $this->raw_response_data = $client->get($this->credentials[$this->context], $template);

        // parse response
        $this->parse();

        // log response
        $this->logSoapResponse();

        // return status
        return $this->raw_response_data['success'];
    }

    /**
     * Function to post a resume to Monster API
     * @param mixed $input
     * @param FileProxy $proxy
     * @return boolean
     */
    public function postResumeToProfile($input, $proxy)
    {
        $timestampBGW = date(DATE_W3C);
        $input['timestampBGW'] = $timestampBGW;

        $input['resume'] = $proxy;
        $input['credentials'] = $this->credentials;

        $view = View::make('api.bgw.power-resume', array(
            'data' => $input,
        ));
        $template = $view->render();

        $client = new SoapRequest();
        $this->raw_response_data = $client->get($this->credentials['BGWPower'], $template);

        // parse response
        $this->parse(new BGWResponseParser());

        // log response
        $this->logSoapResponse();

        // return status
        return $this->raw_response_data['success'];
    }
}
