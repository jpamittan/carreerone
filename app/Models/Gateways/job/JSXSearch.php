<?php
namespace Application\Models\Gateways\Job;

use Application\Models\Containers\SearchResult;
use Application\Models\Entities\Job;
use Application\Models\Gateways\Http\CurlRequest;
use ConfigProxy;
use DateTime;
use DB;

/**
 * Gateway class to monster job integration
 */
class JSXSearch implements SearchGateway
{
    /**
     * Placeholder for seach url
     * @var string
     */
    protected $search_url = null;

    /**
     * Placeholder for power mode flag
     * @var integer
     */
    protected $power_mode = 0;

    /**
     * Placeholder for search parameters
     * @var mixed
     */
    protected $search_parameters = array();

    /**
     * Placeholder for search result
     * @var SearchResult
     */
    protected $search_result = null;

    protected $additional_query = '';

    /**
     * Parameter Mappings
     * @var mixed
     */
    protected $parameter_mappings = array(
        'job_title' => 'tjt',
        'company_name' => 'cn',
        'job_type' => 'jt',
        'cat' => 'cat',
        'country' => 'cy',
        'board' => 'jb',
        'location' => 'lid',
        'location_lid' => 'location_lid',
        'offset' => 'start',
        'limit' => 'pp',
        'skills' => 'q',
        'post_code' => 'zip',
        'job_category' => 'jcat',
        'industry' => 'indid',
        'posting_date' => 'tm',
        'career_level' => 'lv',
        'salary_type' => 'saltyp',
        'salary_min' => 'salmin',
        'education' => 'eid',
        'xcode' => 'co',
        'page' => 'page',
        'occupation' => 'occ',
        'sort' => 'sort',
        'distance' => 'rad',
        'job_status' => 'jsta',
        'state' => 'sid',
        'keyword' => 'q',
        'excl_keyword' => 'xq',
        'company_adv' => 'cn',
        'where' => 'where',
    );

    /**
     * Constructor with mode of operation
     * @param $mode string
     */
    public function __construct($board, $power_mode = 0)
    {
        $config = ConfigProxy::get('careerone');

        // set mode
        $this->power_mode = $power_mode;

        if ($this->power_mode) {
            $this->search_url = $config['JSX'];
            //temp hack
            //$this->search_url = $config['PJSX'];
        } else {
            $this->search_url = $config['JSX'];
        }
        $search_parameters['country'] = 'au';

        // get access details based on board
        $access_info = array_get($config['Boards'], $board, null);
        $this->search_url .= '&jb=' . $access_info['BoardID'];
        $this->search_url .= '&cat=' . $access_info['CAT'];
    }

    /**
     * Getter function for the query
     */
    public function getQueryUrl()
    {
        return $this->search_url;
    }

    /**
     * Getter function for the query
     */
    public function setAdditionalQuery($query)
    {
        $this->additional_query = $query;
    }

    /**
     * Function to set parameters for the search
     * @param mixed $parameters
     * @return boolean
     */
    public function setParameters($parameters)
    {

        $this->search_parameters = array();

        foreach ($parameters as $key => $value) {
            // dont need to include any params that doesnt have a value
            if (empty($value) || is_null($value)) {
                continue;
            }
            $query_param = array_get($this->parameter_mappings, $key, null);
            if (!is_null($query_param)) {
                $this->search_parameters[$query_param] = $value;
            }
        }
        
        if (empty($this->search_parameters['cy'])) {
            $this->search_parameters['cy'] = 'au';
        }

        // apply any replacements or rule exceptions
        if (array_key_exists('lid', $this->search_parameters)) {
            $value = $this->search_parameters['lid'];
            $location = \DB::table('location')
                ->where('city', 'like', $value . '%')
                ->first();
            if ($location) {
                $this->search_parameters['lid'] = $location->map_location_id;
            } else {
                $this->search_parameters['where'] = $value;
                unset($this->search_parameters['lid']);
            }
        }

        $id = '';

        if (array_key_exists('j_a', $parameters)) {
            $id = $parameters['j_a'];
        } elseif (array_key_exists('j_l', $parameters)) {
            $id = $parameters['j_l'];
        } elseif (array_key_exists('j_c', $parameters)) {
            $id = $parameters['j_c'];
        }

        if ($id) {
            $search_location = \DB::table('search_location')->find($id);

            $url = '';
            if ($search_location) {
                $url = urldecode($search_location->url);
                if(strpos($url, '&') > -1) {
                    $this->additional_query = $url;
                } else {
                    $this->search_parameters['lid'] = $url;
                }
            }
        }

        //apply exception replacement for Widget (AIPE) to bypass original lid mapping
        if (array_key_exists('location_lid', $this->search_parameters)) {
            $value = $this->search_parameters['location_lid'];
            $this->search_parameters['lid'] = $value;
            unset($this->search_parameters['location_lid']);
        }

        if (!empty($parameters['additionalQuery'])) {
        	$this->setAdditionalQuery($parameters['additionalQuery']);
        }

        if ($this->power_mode) {
            if(isset($this->search_parameters['start'])){
                $offset = $this->search_parameters['start'];
                unset($this->search_parameters['start']);
                $this->search_parameters['page'] = (int)($offset / $this->search_parameters['pp']);
            }
        }

        // form query
        $this->formQuery();
    }

    /**
     * Function to perform search with pre setted parameters
     * @return SearchResult;
     */
    public function performSearch()
    {
        // Run the request
        $request = new CurlRequest();
        $results = $request->get($this->search_url);

        // add to log
        $this->logSearch($results);

        // parse the ouput
        if ($results["success"]) {
            // parse results
            $this->search_result = $this->parse($results['data']);

            // return SearchResult object
            return $this->search_result;
        }
        return null;
    }

    /**
     * Private function to form query based on parameters set
     */
    private function formQuery()
    {
        // form query
        foreach ($this->search_parameters as $key => $value) {
            $q_key = "&" . $key . "=";
            if (is_array($value)) {
                $search_url_part = "";
                foreach ($value as $item) {
                    $search_url_part .= $q_key . urlencode($item);
                }
                $this->search_url .= $search_url_part;
            } else {
                $this->search_url .= $q_key . urlencode($value);
            }
        }
        $this->search_url .= $this->additional_query;
    }

    /**
     * Private function to parse the results xml string to a SearchResult object
     * @param string $results
     * @return SearchResult
     */
    private function parse($results)
    {
        if (empty($results)) {
            return null;
        }

        // form a result object
        $search_result = new SearchResult();

        // parse document
        $doc = new \DOMDocument();
        $doc->loadXML($results);
        $output = $this->nodeToArray($doc->documentElement);

        $jobs = array_get($output, 'Jobs', null);
        if (isset($jobs)) {
            $search_result->found = $jobs["@attributes"]["Found"];
            $search_result->returned = $jobs["@attributes"]["Returned"];

            $job_node = array_get($jobs, 'Job', null);

            if (isset($job_node)) {
                $returned_jobs = isset($job_node[0]) ? $job_node : array($job_node);

                // format jobs and assign to result
                $search_result->items = $this->formatJobs($returned_jobs);
            }
        }

        // look for errors
        $errors = array_get($output, 'Errors', null);
        if (isset($errors)) {
            $error_msg = $errors['Error']['Message'];
            $search_result->found = 0;
            $search_result->returned = 0;
            $search_result->items = array();
            $search_result->errors = $error_msg;
        }
        return $search_result;
    }

    /**
     * Private function to form jobs according to the db format
     * @param mixed $jobs
     * @return mixed $jobs
     */
    private function formatJobs($jobs)
    {
        // loop through jobs
        $processed_jobs = array();
        foreach ($jobs as $job) {
            $processed = array();
            $processed['score'] = array_get($job, 'Score', null);
            $processed['title'] = array_get($job, 'Title', null);
            $processed['summary'] = array_get($job, 'Summary', null);

            // convert date time active to date time
            $date_active = array_get($job, 'DateActive', null);
            if ($date_active) {
                $date_active_obj = new DateTime($date_active);
                $date_active = $date_active_obj->format('Y-m-d');
            }
            $processed['date_active'] = $date_active;

            // convert expires at to date
            $expires_at = array_get($job, 'DateExpires', null);
            if ($expires_at) {
                $expires_at_obj = new DateTime($expires_at);
                $expires_at = $expires_at_obj->format('Y-m-d');
            }
            $processed['expires_at'] = $expires_at;

            $processed['company_name'] = array_get($job, 'CompanyName', null);
            $processed['company_xcode'] = array_get($job, 'CompanyXcode', null);
            $processed['address_type'] = array_get($job, 'AddressType', null);
            $processed['address_type_id'] = array_get($job, 'AddressTypeID', null);
            $processed['display_options'] = array_get($job, 'display_options', null);

            // location extract
            $location = array_get($job, 'Location', null);
            $processed['country'] = array_get($location, 'Country', null);
            $processed['state'] = array_get($location, 'State', null);
            $processed['city'] = array_get($location, 'City', null);
            if (is_array($processed['city'])) {
                if (count($processed['city']) > 1) {
                    $processed['city'] = $processed['city'][1];
                } else {
                    $processed['city'] = '';
                }
            }
            $processed['post_code'] = array_get($location, 'PostalCode', null);

            // attributes extract
            $attributes = array_get($job, '@attributes', null);
            $processed['position_id'] = array_get($attributes, 'PositionID', null);
            $processed['code'] = array_get($attributes, 'ID', null);

            // add to processed
            $code = $processed['code'];
            $processed_jobs[$code] = $processed;
        }
        return $processed_jobs;
    }

    /**
     * Private function to covert domnote to an array
     * @param $node
     * @return mixed
     */
    private function nodeToArray($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->nodeToArray($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;

                        if ($t == 'DateActive') {
                            $a = array();
                            foreach ($child->attributes as $attr_name => $attr_node) {
                                $a[$attr_name] = (string) $attr_node->value;
                            }
                            $output[$t] = $a['Date'];
                        }

                        if ($t == 'DateExpires') {
                            $a = array();
                            foreach ($child->attributes as $attr_name => $attr_node) {
                                $a[$attr_name] = (string) $attr_node->value;
                            }
                            $output[$t] = $a['Date'];
                        }
                    } elseif ($v) {
                        $output = (string) $v;
                    }
                }

                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attr_name => $attr_node) {
                            $a[$attr_name] = (string) $attr_node->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != '@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }

    /**
     * Private Function to log Job search request status
     * @param string $error_msg
     * @param mixed $details
     * @param mixed $time_taken
     * @param mixed $http_code
     */
    private function logSearch($result)
    {
        $data = array();
        $data['query'] = $this->search_url;
        $data['error_message'] = $result['error_message'];
        $data['time_taken'] = $result['time_taken'];
        $data['code'] = $result['code'];
        $data['ip'] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : '';
        $data['performed_on'] = new DateTime;

        // details only if it's a failure
        $data['details'] = null;
        if (!$result['success']) {
            $data['details'] = $result['data'];
        }

        // log to db
        DB::table('job_search_log')->insert($data);
    }

    public function isPowerMode()
    {
        return ($this->power_mode) ? true : false;
    }

    /**
     * Function to perform search from saved searches
     * @return SearchResult;
     */
    public function runSavedSearch($subscriber)
    {   
        // Run the request
        $request = new CurlRequest();
        $query_str = preg_replace('/[?&]page=[^&]+$|([?&])page=[^&]+&/', '$1', $subscriber->search_query);
        $results = $request->get($this->search_url.$query_str);

        // parse the ouput
        if ($results["success"]) {
            // parse results
            $this->search_result = $this->parse($results['data']);

            // return SearchResult object
            return $this->search_result;
        }
        return null;
    }
}
