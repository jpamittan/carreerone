<?php

namespace App\Models\Gateways\Redact;

use App\Models\Containers\ResumeExtract;
use App\Models\Gateways\RedactGateway;
use App\Models\Proxies\FileProxy;
use Exception;
use Format;
use Guzzle\Service\Client;
use Config;

/**
 * Implementation of Gateway class
 */
class ResumeRedact implements RedactGateway {

    /**
     * Config options
     */
    private $config = array(
        'endpoint' => null,
        'username' => null,
        'password' => null,
        'auth' => null,
    );

    /**
     * Error placeholder
     * @var mixed
     */
    private $error = array(
        'code' => 0,
        'message' => '',
        'details' => null,
    );

    /**
     * Placeholder for post params
     * @var mixed
     */
    private $meta_data = array(
        'meta' => array(
        // fill in post params
        ),
    );

    /**
     * Placeholder array for extracted profile info
     * @var mixed
     */
    protected $extract = null;

    /**
     * Constructor
     */
    public function __construct() {
  $this->config['endpoint'] = Config::get('jojari.redact.url');
    // $this->config['endpoint1'] = 'https://entity-graph-dot-jojari-staging.appspot.com/similar/';
//        $this->config['username'] = 'careerone';
//        $this->config['password'] = 'Hb1abUN8EB3JcP4b';
//        $this->config['auth'] = 'Basic';
        $this->config['username'] = Config::get('jojari.redact.username');
        $this->config['password'] =Config::get('jojari.redact.password');
        $this->config['auth'] = Config::get('jojari.redact.auth');

        $this->extract = new ResumeExtract();
    }

    /**
     * Implemented function to clean uploaded resume
     * @param FileProxy $resume
     */
    public function clean(FileProxy $proxy) {
        try {
            $file_written = false;
            $client = new Client(url('/'));
            $client->setDefaultOption('auth', array($this->config['username'],
                $this->config['password'],
                $this->config['auth']));
            // $client->setDefaultOption('proxy', '192.168.1.110:8888');

            if (is_null($proxy->path)) {
                $proxy->writeContentsToFile();
                $file_written = true;
            }

            // process the request
            $request = $client->post($this->config['endpoint'], array(), $this->meta_data)
                    ->addPostFile('inputfile', $proxy->path);
            $response = $client->send($request);

            // parse response
            $status = $this->parse($response);

            if ($file_written) {
                $proxy->unlink(false);
            }

            return $this->extract;
        } catch (Exception $exp) {
            //echo $code = $exp->getCode(); 
            var_dump($exp->getTraceAsString()); exit;
            var_dump($exp->getLine());
            var_dump($exp->getMessage()); exit;
            $this->error = array(
                'code' => (($code == 0) ? 500 : $code),
                'message' => $exp->getMessage(),
            );
        }
        return null;
    }

    /**
     * Implemented public function to get the error of last executed command
     * @return mixed
     */
    public function getLastError() {
        return $this->error;
    }

    /**
     * Implemented function to set meta data for the request
     * @return mixed|null
     */
    public function setMetaData(array $meta) {
        $this->meta_data['meta'] = $meta;
    }

    /**
     * Private function to parse api response
     * @param $response
     * @return boolean
     */
    private function parse($response) {
        // extract content
        $content = $response->json();

        $status_code = $content['statusCode'];

        // success
        if ($status_code == '200') {

            $content_response = $content['response'];
            // echo '<pre>';
            //     print_r($content_response);exit;
            // parse personal info
            $redacted_meta = array_get($content_response, 'redactedMeta', null);
            $status = $this->parsePersonalInfo($redacted_meta);

            // parse address info
            $addresses = array_get($content_response, 'addresses', null);
            $addr_status = $this->parseAddressInfo($addresses);
                // parse Careerpath info
             $careerpath = array_get($content_response, 'careerpath', null);
            $status = $this->parsePersonalInfo($careerpath);

            // parse education info
            $education_info = array_get($content_response, 'education', null);
            $edu_status = $this->parseEducationInfo($education_info);

            // parse experience info
            $work_history = array_get($content_response, 'jobTitles', null);
            $emp_status = $this->parseWorkHistoryInfo($work_history);

            // parse skills info
            $skills = array_get($content_response, 'skills', null);
            $skills_status = $this->parseSkillsInfo($skills);

            // parse industries
            $industries = array_get($content_response, 'industries', null);
            $industry_status = $this->parseIndustries($industries);
            $this->extract->extra_info['text'] = array_get($content_response, 'redactedText', null);
            // validate status
            if ($status && $addr_status && $edu_status && $emp_status && $skills_status) {
                return true;
            }
        } else {
            $this->error = array(
                'code' => $status_code,
                'message' => $content['errors'][0],
            );
        }
        return false;
    }

    /**
     * Private function to parse personal information
     * @param stdClass $input
     * @return boolean
     */
    private function parsePersonalInfo($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }
        // personal info
        $this->extract->personal_info['personal_email'] = array_get($input, "exEmail", null);

        $raw_data = array_get($input, "exPhone", null);
        $phone_numbers = explode(';', $raw_data);
        if (empty($phone_numbers)) {
            $phone_numbers = $raw_data;
        }
        $this->extract->personal_info['phone_number'] = $phone_numbers;
        $this->extract->personal_info['first_name'] = array_get($input, "firstname", null);
        $this->extract->personal_info['last_name'] = array_get($input, "lastname", null);
        $this->extract->personal_info['full_name'] = array_get($input, "fullname", null);
        $this->extract->personal_info['salary'] = array_get($input, "bayes-job-salary", null);
        $this->extract->personal_info['industry'] = array_get($input, "bayes-industry", null);
    }

    /**
     * Private function to parse address information
     * @param stdClass $input
     * @return boolean
     */
    private function parseAddressInfo($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }

        // addresses info
        foreach ($input as $i => $address) {
            $this->extract->address_info[$i]['full'] = array_get($address, "full", null);
            $this->extract->address_info[$i]['number'] = array_get($address, "number", null);
            $this->extract->address_info[$i]['street'] = array_get($address, "street", null);
            $this->extract->address_info[$i]['suburb'] = array_get($address, "suburb", null);
            $this->extract->address_info[$i]['state'] = array_get($address, "state", null);
            $this->extract->address_info[$i]['postcode'] = array_get($address, "postcode", null);
        }
    }

    /**
     * Private function to parse education information
     * @param mixed $input
     * @return boolean
     */
    private function parseEducationInfo($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }

        $educations = array();
        foreach ($input as $key => $info) {
            $educations[$key] = array();
            $educations[$key]['institution'] = array_get($info, 'institution', null);
            $educations[$key]['country_code'] = array_get($info, 'country_code', null);

            // education level
            $educations[$key]['qualification'] = array_get($info, 'qualification', null);
            $educations[$key]['full'] = array_get($info, 'full', null);

            // save start date
            $start_date = array_get($info, 'start', null);

            if ($start_date) {
               $educations[$key]['start_date'] = $start_date;
               //Format::createDateFromString($start_date);
            }

            // save end date
            $end_date = array_get($info, 'end', null);
            if ($end_date) {
               $educations[$key]['end_date'] = $end_date;
               //Format::createDateFromString($end_date);
            }
        }
        $this->extract->education_info = $educations;
        return true;
    }

    /**
     * Private function to parse work history information
     * @param mixed $input
     * @return boolean
     */
    private function parseWorkHistoryInfo($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }

        $work_history = array();
        foreach ($input as $key => $info) {
            $work_history[$key] = array();
            $work_history[$key]['job_title'] = array_get($info, 'jobTitle', null);
            $work_history[$key]['description'] = array_get($info, 'summary', null);
            $work_history[$key]['skills'] = array_get($info, 'skills', array());
            $work_history[$key]['organisation'] = array_get($info, 'company', null);
            // not providing
            $work_history[$key]['region'] = array_get($info, 'region', null);
            $work_history[$key]['country_code'] = array_get($info, 'country_code', null);
            $work_history[$key]['industry_id'] = array_get($info, 'industry_id', null);
            $work_history[$key]['functional_area_id'] = array_get($info, 'functional_area_id', null);
            $work_history[$key]['career_level_id'] = array_get($info, 'career_level_id', null);

            $work_history[$key]['predictedSkills'] = array_get($info, 'predictedSkills', null);


            // save start date
            $start_date = array_get($info, 'start', null);
            if ($start_date) {
               $work_history[$key]['start_date'] = $start_date;
               // Format::createDateFromString($start_date);
            }

            // save end date
            $end_date = array_get($info, 'end', null);
            if ($end_date) {
                $work_history[$key]['end_date'] = $end_date;
                // Format::createDateFromString($end_date);
                $work_history[$key]['active_job'] = 0;
            } else {
                $work_history[$key]['active_job'] = 1;
            }
        }
        $this->extract->work_history = $work_history;
        return true;
    }

    /**
     * Private function to parse skills information
     * @param mixed $input
     * @return boolean
     */
    private function parseSkillsInfo($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }

        $skills = array();
        foreach ($input as $info) {
            $skills[] = $info;
        }
        $this->extract->skills = $skills;
        return true;
    }

    /**
     * Private function to parse industry information
     * @param mixed $input
     * @return boolean
     */
    private function parseIndustries($input) {
        if (is_null($input) || empty($input)) {
            return false;
        }
        $this->extract->industries = $input;
    }

    //  public function clean1($title) {
    //     try {
    //         $file_written = false;
    //         $client = new Client(url('/'));
    //         $client->setDefaultOption('auth', array($this->config['username'],
    //             $this->config['password'],
    //             $this->config['auth']));
           
    //         $request = $client->post($this->config['endpoint'], array(), $this->meta_data)
    //                 ->addPostFile('inputfile', $proxy->path);
    //         $response = $client->send($request);

    //         // parse response
    //         $status = $this->parse($response);

    //         if ($file_written) {
    //             $proxy->unlink(false);
    //         }

    //         return $this->extract;
    //     } catch (Exception $exp) {
    //         //echo $code = $exp->getCode(); 
    //         var_dump($exp->getTraceAsString()); exit;
    //         var_dump($exp->getLine());
    //         var_dump($exp->getMessage()); exit;
    //         $this->error = array(
    //             'code' => (($code == 0) ? 500 : $code),
    //             'message' => $exp->getMessage(),
    //         );
    //     }
    //     return null;
    // }

}
