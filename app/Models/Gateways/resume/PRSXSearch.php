<?php
namespace Application\Models\Gateways\Resume;

use Application\Models\Containers\SearchResult;
use Application\Models\Gateways\Http\CurlRequest;
use ConfigProxy;

/**
 * Gateway class implementing PRSXSearch for resumes
 */
class PRSXSearch
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
        'job_title' => 'jt',
        'education' => 'minedulvid',
        'offset' => 'page',
        'limit' => 'pagesize',
        'mdatemaxage' => 'mdatemaxage',
        'location' => 'loc', //location-distance
        'skills' => 'sk',
        'keywords' => 'sk',
        'query' => 'q',
    );

    /**
     * Placeholder for education levels
     * @var mixed
     */
    protected $education_levels = array(
        1 => 'High School or equivalent',
        2 => 'Certificate Level',
        3 => 'Diploma / Advanced Diploma',
        4 => 'Associate Degree',
        5 => 'Bachelors Degree',
        6 => 'Masters Degree',
        7 => 'Doctorate Degree',
        8 => 'Other Professional Degree',
        9 => 'Some University Coursework Completed',
        12 => 'Completion / Leaving Certificate',
    );

    /**
     * Constructor with mode of operation
     * @param $mode string
     */
    public function __construct($board)
    {
        $config = ConfigProxy::get('careerone');
        $this->search_url = $config['PRSX'];

        // get access details based on board
        $access_info = array_get($config['Boards'], $board, null);
        $this->search_url .= '&jb=' . $access_info['BoardID'];
        $this->search_url .= '&cat=' . $config['PRSX_CAT']; // $access_info['CAT'];
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
        foreach ($parameters as $key => $value) {
            $query_param = array_get($this->parameter_mappings, $key, null);
            if (!is_null($query_param)) {
                $this->search_parameters[$query_param] = $value;
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
        // Run the request
        $request = new CurlRequest();
        $results = $request->get($this->search_url);

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
                if ($key == 'sk') {
                    $filtered = array_map(function ($item) {
                        return $this->cleanParameter($item . '|NTH|');
                    }, $value);
                    $this->search_url .= $q_key . implode(',', $filtered);
                } else {
                    $this->search_url .= implode($q_key, $value);
                }
            } else {
                $this->search_url .= $q_key . $this->cleanParameter($value);
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

        $resumes = array_get($output, 'Resumes', null);
        if (isset($resumes)) {
            $search_result->found = $resumes["@attributes"]["Found"];
            $search_result->returned = $resumes["@attributes"]["Returned"];

            $resume_node = array_get($resumes, 'Resume', null);

            if (isset($resume_node)) {
                $returned_resumes = isset($resume_node[0]) ? $resume_node : array($resume_node);

                // format resumes and assign to result
                $search_result->items = $this->formatResumes($returned_resumes);
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
                            foreach ($child->attributes as $attrName => $attrNode) {
                                $a[$attrName] = (string) $attrNode->value;
                            }
                            $output['DateActiveUTC'] = $a['Date'];
                        }
                    } elseif ($v) {
                        $output = (string) $v;
                    }
                }

                if (is_array($output)) {
                    if ($node->attributes->length) {
                        $a = array();
                        foreach ($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
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
     * Private function to form resumes according to the required format
     * @param mixed $resumes
     * @return mixed $resumes
     */
    private function formatResumes($resumes)
    {
        // loop through resumes
        $processed_resumes = array();
        foreach ($resumes as $resume) {
            $processed = array();
            $processed['relevance'] = array_get($resume, 'Relevance', null);
            $processed['title'] = array_get($resume, 'ResumeTitle', null);

            // attributes extract for sid
            $attributes = array_get($resume, '@attributes', null);
            $sid = array_get($attributes, 'SID', null);

            // date modified extract
            $date_modified = array_get($resume, 'DateModified', null);
            $dm_attributes = array_get($date_modified, '@attributes', null);
            $processed['date_modified'] = array_get($dm_attributes, 'Date', null);

            // name extract
            $personal_info = array_get($resume, 'PersonalData', null);
            $name_info = array_get($personal_info, 'Name', null);
            $processed['first_name'] = array_get($name_info, 'First', null);
            $processed['last_name'] = array_get($name_info, 'Last', null);

            // skills extract
            $skills = array_get($resume, 'Skills', null);
            $processed['skills'] = $this->extractSkills($skills);

            // add to processed resumes
            $processed_resumes[$sid] = $processed;
        }
        return $processed_resumes;
    }

    /**
     * Private function to extract matched skills from the skills node
     * @param mixed $skiils
     * @return mixed $skills
     */
    private function extractSkills($returned_skills)
    {
        $extracted = array();
        $matched = array();

        // parse skills
        if (!is_null($returned_skills) && !empty($returned_skills)) {
            $skills = array_get($returned_skills, 'Skill', null);
            if (isset($skills)) {
                foreach ($skills as $skill) {
                    if (is_array($skill)) {
                        $extracted[] = $skill["Name"];
                        if (isset($skill["Matches"]) && is_array($skill["Matches"]) && sizeof($skill["Matches"]) > 0) {
                            $matched[] = $skill["Name"];
                        }
                    } else {
                        $extracted[] = $skill;
                    }
                }
            }
        }
        return array(
            'all' => $extracted,
            'matched' => $matched,
        );
    }

    /**
     * Private functon to clean parameter passed to the query
     */
    private static function cleanParameter($parameter)
    {
        $parameter = preg_replace("/[+]req$/", "|REQ|", $parameter);
        $parameter = preg_replace("/[+]nth$/", "|NTH|", $parameter);
        $parameter = str_replace("&", "%2526", $parameter);
        $parameter = str_replace(" ", "%2520", $parameter);
        $parameter = str_replace("/", "%252f", $parameter);
        $parameter = str_replace("+", "%252b", $parameter);
        $parameter = str_replace("|NTH|", "+nth", $parameter);
        $parameter = str_replace("|REQ|", "+req", $parameter);
        return $parameter;
    }
}
