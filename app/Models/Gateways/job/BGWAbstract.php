<?php
namespace Application\Models\Gateways\Job;

use Application\Models\Gateways\SAXParserGateway;
use DB;
use Util;

abstract class BGWAbstract
{
    /**
     * Placeholder for context
     * @var array
     */
    protected $context = null;

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
    protected function parse(SAXParserGateway $bgw_response_parser)
    {
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
        $data['query'] = $this->credentials[$this->context] . '|Username:' . $this->credentials['Username'];

        if ($this->parsed_response_data['status'] == 'failure') {
            $data['error_message'] = $this->parsed_response_data['error'][0];
        } elseif (!$this->raw_response_data['success']) {
            $data['error_message'] = $this->raw_response_data['code'] . " - " . $this->raw_response_data['error_message'];
        }

        $data['time_taken'] = $this->raw_response_data['time_taken'];
        $data['code'] = $this->raw_response_data['code'];
        $data['ip'] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : '';
        $data['performed_on'] = new \DateTime();

        // details only if it's a failure
        $data['details'] = serialize($this->parsed_response_data);

        // log to db
        DB::table('job_search_log')->insert($data);
    }

    /**
     * Getter function for return parsed response data
     * @return mixed
     */
    public function getParsedResponseData()
    {
        // placeholder for response info
        $response = array(
            'status' => '',
            'result' => array(),
            'messages' => array(),
        );

        // parse response with gateway
        $data = $this->parsed_response_data;
        if (!is_null($data) && !empty($data)) {
            if ($data['status'] == 'failure') {
                if (count($data['error']) > 0) {
                    foreach ($data['error'] as $error) {
                        $lang_key = "";

                        switch ($error) {
                            case 'The user doesnt have the license BGW_Manage_JobSeekers':
                                $response['messages'][] = 'wrong_profile.account_api_license';
                                break;
                            case 'Email Address is not unique in namespace':
                                $response['messages'][] = 'wrong_email_username';
                                break;
                            case 'EmailAddress [] is invalid.':
                                $response['messages'][] = 'invalid_email';
                                break;
                            case 'The max resume count 5 has been exceeded':
                                $response['messages'][] = 'reached_max_resumes';
                                break;
                            case 'reason Invalid File Type, message The uploaded file has error format!':
                                $response['messages'][] = 'wrong_file_extension';
                                break;
                            case 'UserName is not unique in namespace':
                                $response['messages'][] = 'username_not_unique';
                                break;
                            case 'Illegal filename provided, Error Illegal characters in path.':
                                $response['messages'][] = 'invalid_filename';
                                break;
                            case 'The recruiter that created the job seeker is missing':
                                $response['messages'][] = 'missing_recruiter';
                                break;
                            default:
                                $response['messages'][] = $this->matchOtherErrors($error);
                                break;
                        }
                    }
                    $response['status'] = 'error';
                } elseif (isset($data['error_levels']['fault'])) {
                    $response['status'] = 'error';
                    $response['messages'][] = 'wrong_bgw_api_credentials';
                } else {
                    $response['status'] = 'error';
                    $response['messages'][] = 'unknown_error';
                }
            } else {
                $response['result'] = Util::array_get($data, 'result', null);
                $response['status'] = 'success';
            }
        }
        return $response;
    }

    private function matchOtherErrors($error)
    {
        $key = 'exception';
        $match = preg_match('/is longer than the allowed maximum of/', $error);
        if ($match === 1) {
            $key = 'username_too_long';
        }
        return $key;
    }
}
