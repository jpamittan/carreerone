<?php
namespace Application\Models\Gateways\Job;

use Application\Models\Gateways\Http\SoapRequest;
use Application\Models\Gateways\Parser\BGWResponseParser;
use Application\Models\Gateways\Parser\RejuvenatorJobParser;
use DB;
use View;
use Application\Models\Gateways\Encrypt;
class BGWBroker
{
    /**
     * Placeholder for crendentials
     * @var array
     */
    protected $credentials = array();

    /**
     * Placeholder for parsed response data
     * @var null
     */
    protected $parsed_response_data = null;

    /**
     * Placeholder for raw response data
     * @var null
     */
    protected $raw_response_data = null;

    /**
     * Construct
     */
    public function __construct($credentials)
    {
        $this->credentials = $credentials;

    }

    /**
     * Function to retrieve jobs from Monster API
     * @param integer $job_id
     * @return integer
     */
    public function getJob($job_id)
    {
        $timestampBGW = date(DATE_W3C);
        $view = View::make('api.bgw.broker-job-search', array(
            'timestampBGW' => $timestampBGW,
            'user' => $this->credentials['Username'],
            'password' => $this->credentials['Password'],
            'job' => $job_id,
            'boardID' => $this->credentials['BoardID'], // ???
        )
        );
        $template = $view->render();

        $client = new SoapRequest();
        $this->raw_response_data = $client->get($this->credentials['BGWBroker'], $template);

        // parse response
        $this->parseJob();

        // log response
        $this->logSoapResponse();

        // return status
        return $this->raw_response_data['success'];
    }

    /**
     * Function to post jobs to Monster API
     * @param integer $job_id
     * @return boolean
     */
    public function postJob($job)
    {
        $timestampBGW = date(DATE_W3C);
        $view = View::make('api.bgw.broker-job-post', array(
            'timestampBGW' => $timestampBGW,
            'user' => $this->credentials['Username'],
            'password' => $this->credentials['Password'],
            'job' => $job,
            'boardID' => $this->credentials['BoardID'],
        )
        );
        $template = $view->render();

        $client = new SoapRequest();
        $this->raw_response_data = $client->get($this->credentials['BGWBroker'], $template);

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

        $view = View::make('api.bgw.broker-resume', array(
            'data' => $input,
        ));
        $template = $view->render();

        $client = new SoapRequest();
        $this->raw_response_data = $client->get($this->credentials['BGWBroker'], $template);

        // parse response
        $this->parse();

        // log response
        $this->logSoapResponse();

        // return status
        return $this->raw_response_data['success'];
    }

    /**
     * Getter function for return parsed response data
     * @return mixed
     */
    public function getParsedResponseData()
    {
        return $this->parsed_response_data;
    }

    /**
     * Getter function to return unparsed response
     * @return mixed
     */
    public function getRawResponseData()
    {
        return $this->raw_response_data;
    }

    /**
     * Function to parse BGW response
     */
    protected function parse()
    {
        $bgw_response_parser = new BGWResponseParser();
        $parser = xml_parser_create("UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($parser, $bgw_response_parser);
        xml_set_element_handler($parser, "startTag", "endTag");
        xml_set_character_data_handler($parser, "tagData");
        xml_parse($parser, $this->raw_response_data['data'], 0);
        $this->parsed_response_data = $bgw_response_parser->getResponse();
    }

    /**
     * Function to parse BGW response
     */
    protected function parseJob()
    {
        $bgw_response_parser = new RejuvenatorJobParser();
        $parser = xml_parser_create("UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);
        xml_set_object($parser, $bgw_response_parser);
        xml_set_element_handler($parser, "startTag", "endTag");
        xml_set_character_data_handler($parser, "tagData");
        xml_parse($parser, $this->raw_response_data['data'], 0);
        $this->parsed_response_data = $bgw_response_parser->getResponse();
    }

    /**
     * Function to log the response from api either it's a failure or a success
     */
    protected function logSoapResponse()
    {
        $data = array();
        $data['query'] = $this->credentials['BGWBroker'] . '|Username:' . $this->credentials['Username'];

        $data['code'] = $this->raw_response_data['code'];
        if (array_get($this->parsed_response_data, 'status', '') == 'failure') {
            $data['code'] = 500;
            $data['error_message'] = $this->parsed_response_data['error'][0];
        } elseif (!$this->raw_response_data['success']) {
            $data['code'] = 501;
            $data['error_message'] = $this->raw_response_data['code'] . " - " . $this->raw_response_data['error_message'];
        }

        $data['time_taken'] = $this->raw_response_data['time_taken'];
        $data['ip'] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : '';
        $data['performed_on'] = new \DateTime();

        // details only if it's a failure
        $data['details'] = serialize($this->parsed_response_data);

        // log to db
        \DB::table('job_search_log')->insert($data);
    }

     /**
     * Function to post a resume to Monster API
     * @param mixed $input
     * @param EmployeeProfile $profile
     * @param FileProxy $proxy
     * @return boolean
     */
    public function postResumeToMonsterProfile($input, $profile, $proxy)
    {
        $data = array();
        $timestampBGW = date(DATE_W3C);
        $data['timestampBGW'] = $timestampBGW;
        $data['employee'] = $profile;

 
        $data['employee_email'] = $profile->email;;
       
     
        $data['resume'] = $proxy;
        $data['resume']->name = $profile->resume_filename;
     
        $data['credentials'] = $this->credentials;
         
        $data['monsterAccount'] = $profile->email;
        $data['monsterPassword'] = Encrypt::decryptStringMonster($profile->password) ; ;
        $data['ChannelID'] = 168;

     
        $data['city'] = $profile->location_city;
        $data['state'] = $profile->location_state;
        $data['country_code'] = 'AU';
        $data['post_code'] = $profile->location_postcode;
         

        $view = View::make('api.bgw.post-resume-to-profile', array(
            'data' => $data,
        ));
        echo $template = $view->render();
 
        $client = new SoapRequest();
        $this->raw_response_data = $client->get("https://gateway.monster.com:8443/bgwBroker", $template);
        print_r($this->raw_response_data);
        // parse response
        $this->parse(new BGWResponseParser);

        // log response
        $this->logSoapResponse();

        // return status
        return $this->raw_response_data['success'];
    }
}
