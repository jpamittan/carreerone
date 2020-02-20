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
class PJSSXSearch implements SearchGateway
{
    /**
     * Placeholder for seach url
     * @var string
     */
    protected $search_url = null;

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

    /**
     * Parameter Mappings
     * @var mixed
     */
    protected $parameter_mappings = array(
        'job_title' => 'title',
        'company_name' => 'company',
        'job_type' => 'jobtype',
        'cat' => 'cat',
        'country' => 'country',
        'board' => 'board',
        'location' => 'location',
        'offset' => 'start',
        'limit' => 'fetchsize',
        'skills' => 'skill',
        'long_job_title' => 'longtitle',
        'job_created_interval' => 'age',
    );

    /**
     * Constructor with mode of operation
     * @param $mode string
     */
    public function __construct($board)
    {
        $config = ConfigProxy::get('careerone');
        $this->search_url = $config['PJSSX'] . '&country=au';

        // get access details based on board
        $access_info = array_get($config['Boards'], $board, null);
        $this->search_url .= '&board=' . $access_info['BoardID'];
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
     * Function to set parameters for the search
     * @param mixed $parameters
     * @return boolean
     */
    public function setParameters($parameters)
    {
        $not_found = array();
        $this->search_parameters = array();
        foreach ($parameters as $key => $value) {
            $query_param = array_get($this->parameter_mappings, $key, null);
            if (!is_null($query_param)) {
                $this->search_parameters[$query_param] = $value;
            } else {
                $not_found[$key] = $value;
            }
        }

        // handle not defined keys as per business rules
        if (in_array('distance', $not_found)) {
            if (in_array('location', $this->search_parameters)) {
                $location = $this->search_parameters['location'];
                $this->search_parameters['location'] = $location . '--' . $not_found['distance'];
            }
        }

        // form query
        $this->formQuery();
    }

    /**
     * Function to perform search with pre setted parameters
     * @return SearchResult
     */
    public function performSearch()
    {
        $this->search_result = null;

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
                    if (!is_null($item) and !empty($item)) {
                        $search_url_part .= $q_key . urlencode($item);
                    }
                }
                $this->search_url .= $search_url_part;
            } else {
                $this->search_url .= $q_key . urlencode($value);
            }
        }
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
            if (is_array($processed['summary'])) {
                if (count($processed['summary']) > 0) {
                    $processed['summary'] = $processed['summary'][0];
                } else {
                    $processed['summary'] = '';
                }
            }

            // convert date time active to date time
            $date_active = array_get($job, 'DateActive', null);
            if ($date_active) {
                $date_active_obj = new DateTime($date_active);
                $date_active = $date_active_obj->format('Y-m-d');
            }
            $processed['date_active'] = $date_active;

            $processed['company_name'] = array_get($job, 'CompanyName', null);
            if (is_array($processed['company_name'])) {
                if (count($processed['company_name']) > 0) {
                    $processed['company_name'] = $processed['company_name'][0];
                } else {
                    $processed['company_name'] = null;
                }
            }
            $processed['company_xcode'] = array_get($job, 'CompanyXcode', null);
            $processed['address_type'] = array_get($job, 'AddressType', null);
            $processed['address_type_id'] = array_get($job, 'AddressTypeID', null);
            $processed['display_options'] = array_get($job, 'display_options', null);
            $processed['expires_at'] = array_get($job, 'DateExpires', null);

            // location extract
            $location = array_get($job, 'Location', null);
            $processed['country'] = array_get($location, 'Country', null);
            $processed['state'] = array_get($location, 'State', null);
            if (is_array($processed['state'])) {
                if (count($processed['state']) > 0) {
                    $processed['state'] = $processed['state'][0];
                } else {
                    $processed['state'] = null;
                }
            }
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
            $code = array_get($attributes, 'PositionAdID', null);
            $processed['position_id'] = null;

            // add to processed
            $processed['code'] = $code;
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

                        if ($t == 'AddressType') {
                            $a = array();
                            foreach ($child->attributes as $attr_name => $attr_node) {
                                $a[$attr_name] = (string) $attr_node->value;
                            }
                            $output['AddressTypeID'] = $a['AddressTypeID'];
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
        $data['error_message'] = array_get($result, 'error_message', null);
        $data['time_taken'] = array_get($result, 'time_taken', null);
        $data['code'] = array_get($result, 'code', 0);
        $data['ip'] = isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : '';
        $data['performed_on'] = new DateTime;

        // details only if it's a failure
        $data['details'] = null;
        if (!$result['success']) {
            $data['details'] = array_get($result, 'data', null);
        }

        // log to db
        DB::table('job_search_log')->insert($data);
    }
}
