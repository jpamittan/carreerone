<?php

namespace App\Models\Services;

use App\Models\Services\LocationService;
use App\Models\Services\SearchLocationService;
use DB, URL;

class JsxService {
	use \App\Models\Services\XMLToArray;

    CONST SALARY_ANNUAL = 1;
    CONST SALARY_HOUR = 2;
    CONST SALARY_WEEK = 3;
    CONST SALARY_MONTH = 4;

    const JOB_TYPE_PERMANENT = 1;
    const JOB_TYPE_CASUAL = 2;
    const JOB_TYPE_INTERN = 3;
    const JOB_STATUS_FULLTIME = 4;
    const JOB_STATUS_PARTTIME = 5;
    const JOB_STATUS_OTHER = 6;

    protected $location = null;
    protected $searchLocation = null;
    protected $company = null;
    protected $url = 'http://jsx.monster.com/query.ashx?rev=2.1';
    protected $cy = '&cy=au';
    protected $cat = [
        'c1' => '&cat=1:EAAQPTJhLpvm89qNHnFvt5fRdiyyK5MwTh_rRq5YQz5tjjdpmz7yXAU8i.fcnSNt12qu5gHgI.2Y3C25dwlU.6Kd4iKahcmlWgQAjcouKqbQj9Nax6Be6tDSX6ytHO5Hpnwb',
        'adzuna' => '&cat=1:EAAQiV6rJPfu.qQvb1ZJZvLAenBA2LpAdWPKjlCfUNyRQDLVZvLFhZ4_yXSphP5CLEijnciZI2wTzaUDJ9g8YWrRMTjAiEk_7tpdX7r3Gtk1zvNAASBuvrDNWZiz0SMcoWie&jb=7150'
    ];
    protected $locationId = '';
    protected $locationName = 'Australia';
    protected $where = '';
    protected $category = '';
    protected $salaryMin = '';
    protected $salaryMax = '';
    protected $salaryType = '';
    protected $jobType = '';
    protected $jobStatus = '';
    protected $query = '';
    protected $queryBehaviour = '&qb=and';
    protected $postcode = '';
    protected $radius = '';
    protected $locationString = '';
    protected $companyCode = '';
    protected $companyName = '';
    protected $sort = '&sort=rv.di.dt';
    protected $limit = '&pp=20';
    protected $page = '&page=1';
    protected $pageSize = '';
    protected $exwh = '&exwh=0';
    protected $type = 'c1';
    protected $jobURL = 'http://jobview.careerone.com.au/';
    protected $adzunaJobURL = 'http://jobview.careerone.com.au/';
    protected $postSearchFilter = [];
    protected $regionalIds = ['NSW' => '861', 'QLD' => '865', 'WA' => '863', 'VIC' => '867', 'SA' => '866', 'TAS' => '862', 'ACT' => '125', 'NT' => '864'];

    public function __construct(LocationService $location, SearchLocationService $searchLocation ) {
        $this->location = $location;
        $this->searchLocation = $searchLocation;
    }

    public function search($cat = 'c1') {
        $query = $this->url . $this->cy . $this->cat[$cat] . $this->locationId . $this->category;
        $query .= $this->salaryMin . $this->salaryMax . $this->salaryType . $this->jobType;
        $query .= $this->jobStatus . $this->query . $this->queryBehaviour . $this->postcode;
        $query .= $this->radius . $this->locationString . $this->companyCode . $this->exwh;
        $query .=   $this->page . $this->pageSize . $this->where;
        $query .= $this->sort . $this->limit;
        $ch = curl_init();
        if (! $ch) {
            throw new \Exception('cURL Error: Couldn\'t initialize handle');
        }
        curl_setopt($ch, CURLOPT_URL, $query);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        $results = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if (empty($info['http_code'])) {
            throw new \Exception('HTTP Error: No code was returned');
        }
        if ($info['http_code'] != 200){
            throw new \Exception('HTTP Error: HTTP Code - ' . $info['http_code']);
        }
        $jobs = $this->XMLToArray($results);
        $this->type = $cat;
        if (isset($jobs['Jobs']['Job'])) {
            if (! isset($jobs['Jobs']['Job'][0])) {
                $jobs['Jobs']['Job'] = [$jobs['Jobs']['Job']];
            }
            $jobs['Jobs']['Job'] = $this->hydrate($jobs['Jobs']['Job']);
        }
        return $jobs;
    }

    protected function hydrate(array $results) {
        $trim = function($text, $length) {
            if (strlen($text) > $length) {
                $text = substr($text, 0, strpos(wordwrap($text, $length), "\n")) . '...';
            } 
            return $text;
        };
        $ids = [];
        $jobs = [];
        foreach ($results as $job) {
            $jobs[$job['@attributes']['ID']] = $job;
        }
        $results = $jobs;
        foreach ($results as $key => &$job) {
            if (isset($this->postSearchFilter['states'])) {
                foreach ($this->postSearchFilter['states'] as $state) {
                    if (! isset($job['Location']['State'])) {
                        unset($results[$key]);
                        continue(2);
                    } 
                    $match = false;
                    if ($this->regionalIds[$job['Location']['State']] == $state) {
                        $match = true;
                        break;
                    }
                }
                if (! $match) {
                    unset($results[$key]);
                    continue;
                }
            }
            $ids[] = $key;
            if ($this->type == 'adzuna') {
                $job['URL'] = $this->adzunaJobURL . rtrim(preg_replace('%[\-]+%', '-', preg_replace('%[^0-9a-zA-Z\-]+%', '-', $job['Title'])), '-') . '-' . $job['@attributes']['ID'] . '.aspx';
            } else {
                $job['URL'] = $this->jobURL . rtrim(preg_replace('%[\-]+%', '-', preg_replace('%[^0-9a-zA-Z\-]+%', '-', $job['Title'])), '-') . '-' . $job['@attributes']['ID'] . '.aspx';
            }
            $job['source'] = 'careerone';
            $job['DateActive'] =  $job['DateActive'][0];
            $job['DateExpires'] = $job['DateExpires'][0];
            $job['DateUpdated'] = $job['DateUpdated'][0];
            $job['Summary'] = $trim($job['Summary'], 150);
            $job["Days"] = '';
            $job['CompanyImage'] = '';
            $job['Bolded'] = 0;
            if (isset($job["DateActiveUTC"])) {
                $sydneyTZ = date_default_timezone_get();
                $sydney = new \DateTimeZone($sydneyTZ);
                $utc = new \DateTimeZone('UTC');
                $dateXML = new \DateTime($job["DateActiveUTC"], $utc);
                $d1 = new \DateTime($dateXML->format('Y-m-d H:i:s'));
                $date = new \DateTime(date('Y-m-d H:i:s'), $sydney);
                $date->setTimezone($utc);
                $d2 = new \DateTime($date->format('Y-m-d H:i:s'));
                $diff = $d1->diff($d2);
                $days = $diff->y * 365.25 + $diff->m * 30 + $diff->d + $diff->h / 24 + $diff->i / 1440;
                $date->setTimezone($sydney);

                $job['Days'] = floor($days);
            }
            $job['Location']['State'] = isset($job['Location']['State']) ? $job['Location']['State'] : '';
            if (isset($job['Salary'])) {
                $salaryType = isset($job['Salary']['Type']) ? $job['Salary']['Type']['@attributes']['Id'] : self::SALARY_ANNUAL;
                $label = isset($job['Salary']['Type'][0]) ? $job['Salary']['Type'][0] : '';
                $min = isset($job['Salary']['Min']) ? $job['Salary']['Min']['@attributes']['Value'] : null;
                $max = isset($job['Salary']['Max']) ? $job['Salary']['Max']['@attributes']['Value'] : null;
                $string = '';
                if ($min && $max && $min != $max) {
                    $decimals = (strpos('.', $min) || strpos('.', $max)) ? 2 : 0;
                    $string = '$' . number_format($min, $decimals) . ' - ' . '$' . number_format($max, $decimals);
                } else if (($min && $max && $min == $max) || ! $max) {
                    $decimals = (strpos('.', $min)) ? 2 : 0;
                    $string = '$' . number_format($min, $decimals);
                } else if ($max) {
                    $decimals = (strpos('.', $max)) ? 2 : 0;
                    $string = '$' . number_format($max, $decimals);
                }
                $job['Salary'] = array('Type' => $salaryType, 'Label' => $label, 'Min' => $min, 'Max' => $max, 'String' => $string);
            }
        }
        unset($job);
        $jobs = array();
        foreach ($jobs as $job) {
            $results[$job->job_id]['Bolded'] = $job->bolded;
            if (isset($job->has_image) && $job->has_image == '2') {
                $results[$job->job_id]['CompanyImage'] = 'http://media.newjobs.com/' . $job->xcode . '/companylogo.gif';
            } else if (isset($job->has_image) && $job->has_image == '3') {
                $results[$job->job_id]['CompanyImage'] = 'https://securemedia.newjobs.com/clu/' . substr($job->xcode, 0, 4) . '/' . $job->xcode . '/companylogo.gif';
            } else if (isset($job->jsl_path) && ! empty($job->jsl_path)) {
                $results[$job->job_id]['CompanyImage'] = $job->jsl_path;
            }
        }
        return $results;
    }

    public function transformParams($searchParams, $categories, $jobTypes) {
        // This is to make sure parameters from the old SEO searches match current parameters
        $parts = explode('/', $searchParams['job-search']);
        if (empty($parts)) {
            return [];
        }
        $params = [];
        $states = $this->searchLocation->getStates();
        $capitals = array_reverse(['Sydney', 'Melbourne', 'Brisbane', 'Adelaide', 'Perth', 'Hobart', 'Canberra', 'Darwin']);
        $categoryMappings = [
            'accounting' => 'accounting',
            'administration-secretarial' => 'Administration & Secretarial',
            'advertising-media-arts-entertainment' => 'Advertising, Media, Arts & Entertainment',
            'agriculture-nature-animal' => 'Agriculture, Nature & Animal',
            'banking-finance' => 'Banking & Finance',
            'science' => 'Biotech, R&D, Science',
            'career-expos' => 'Career Expos',
            'construction-architecture' => 'Construction, Architecture & Interior Design',
            'customer-service-call-centre' => 'Customer Service & Call Centre',
            'writing' => 'Editorial & Writing',
            'education-childcare-training' => 'Education, Childcare & Training',
            'engineering' => 'Engineering',
            'executive-strategic-management' => 'Executive & Strategic Management',
            'franchise-business-ownership' => 'Franchise & Business Ownership',
            'government-defence' => 'Goverment, Defence & Emergency',
            'hr-recruitment' => 'HR & Recruitment',
            'health-medical-pharmaceutical' => 'Health, Medical & Pharmaceutical',
            'hospitality-travel-tourism' => 'Hospitality, Travel & Tourism',
            'it' => 'IT',
            'insurance-superannuation' => 'Insurance & Superannuation',
            'legal' => 'Legal',
            'logistics-supply-transport' => 'Logistics, Supply & Transport',
            'manufacturing-industrial' => 'Manufacturing & Industrial',
            'marketing' => 'Marketing',
            'mining-oil-gas' => 'Mining, Oil & Gas',
            'other' => 'Other',
            'program-project-management' => 'Program & Project Management',
            'property-real-estate' => 'Property & Real Estate',
            'qa-safety' => 'QA & Safety',
            'retail' => 'Retail',
            'sales' => 'Sales',
            'security-protective-services' => 'Security',
            'trades-services' => 'Trades & Services',
            'voluntary-charity-social-work' => 'Voluntary, Charity & Social Work',
            'work-from-home' => 'Work from Home'
        ];
        $hasLocation = $hasCategory = $hasJobType = false;
        foreach ($parts as $part) {
            if (empty($part)) {
                continue;
            }
            if (! $hasLocation) {
                $check = ucwords(str_replace(['---', '--', '-'], [' & ', ', ', ' '], $part));
                $locationParts = explode(' ', $check);
                $postcode = $state = $suburb = '';
                $hasPostcode = false;
                foreach ($locationParts as $locationPart) {
                    if (is_numeric($locationPart)) {
                        $hasPostcode = true;
                        break;
                    }
                }
                if (count($locationParts) >= 3 && $hasPostcode) {
                    $postcode = array_pop($location);
                    $state = array_pop($location);
                    $suburb = implode(' ', $location);

                    $postcode = (strlen($postcode) == 4) ? $postcode : '0' . $postcode;
                }
                foreach ($capitals as $capital) {
                    $check = str_replace($capital . ' & ', $capital . ' - ', $check);
                }
                switch(true) {
                    case isset($states[strtoupper($check)]):
                        $check = strtoupper($check);
                    case $this->location->findBySuburbCombination($suburb, $state, $postcode):
                    case $this->searchLocation->getByName($check):
                    case $this->location->findBySuburb($check):
                    case $check == 'Australia':
                        $params['search_location'] = $check;
                        $hasLocation = true;
                    break;
                }
                if ($hasLocation) {
                    continue;
                }
            }
            if (! $hasCategory) {
                $part = isset($categoryMappings[$part]) ? $categoryMappings[$part] : $part;
                foreach ($categories as $category) {
                    if (strtolower($category->title) == strtolower($part)) {
                        $params['search_category'] = $category->code;
                        $hasCategory = true;
                        break;
                    }
                }
                if ($hasCategory) {
                    continue;
                }
            }
            if (! $hasJobType) {
                foreach ($jobTypes as $jobType => $name) {
                    if ($jobType == $part) {
                        $params['search_job_type'] = $part;
                        $hasJobType = true;
                        break;
                    }
                }
                if ($hasJobType) {
                    continue;
                }
            }
            if ($part == 'search' || preg_match('%[a-zA-Z]+[0-9]+%', $part)) {
                continue;
            }
            $params['search_keywords'] = str_replace(['---', '--', '-'], [' & ', ', ', ' '], $part);
        }
        if (isset($params['search_location']) && $params['search_location'] == 'Australia') {
            unset($params['search_location']);
        }
        if (isset($searchParams['j_s'])) {
            $params['search_location'] = urldecode($searchParams['j_s']);
        }
        if (isset($searchParams['q'])) {
            $params['search_keywords'] = urldecode($searchParams['q']);
        }
        if (isset($searchParams['WT_mc_n'])) {
            $params['WT.mc_n'] = $searchParams['WT_mc_n'];
        }
        return $params;
    }

    public function applyParams(array $params) {
        if (isset($params['search_keywords']) && ! empty($params['search_keywords'])) {
            $this->setQuery($params['search_keywords']);
        }
        if (isset($params['search_category']) && ! empty($params['search_category'])) {
            $this->setCategory((int) $params['search_category']);
        }
        if (isset($params['search_company']) && ! empty($params['search_company'])) {
            $this->setCompanyCode($params['search_company']);
        }
        if (isset($params['search_company_name']) && ! empty($params['search_company_name'])) {
            $this->setCompanyName($params['search_company_name']);
        }
        $jobType = '';
        if (isset($params['search_job_type'])) {
            switch ($params['search_job_type']) {
                case 'casual':
                    $this->setJobType(self::JOB_TYPE_CASUAL);
                break;
                case 'part-time':
                    $this->setJobStatus(self::JOB_STATUS_PARTTIME);
                break;
                case 'full-time':
                    $this->setJobStatus(self::JOB_STATUS_FULLTIME);
                break;
                default:
                    // Nothing to do here
                break;
            }
            $jobType = $params['search_job_type'];
        }
        if (isset($params['search_location']) && ! empty($params['search_location']) && $params['search_location'] != 'Australia') {
            $location = explode(' ', $params['search_location']);
            $hasPostcode = false;
            foreach ($location as $value) {
                if (is_numeric($value)) {
                    $hasPostcode = true;
                    break;
                }
            }
            $states = $this->searchLocation->getStates();
            $stateCheck = ucwords(strtolower($params['search_location']));
            // If it matches a state name, do state-wide search
            if (isset($states[$stateCheck])) {
                $this->setLocationId($states[$stateCheck]);
                $this->locationName = $params['search_location'];
            } else if (isset($states[strtoupper($params['search_location'])])) {
                $this->setLocationId($states[strtoupper($params['search_location'])]);
                $this->locationName = $params['search_location'];
            } else if ( ! $hasPostcode) {
                // Somewhat blatant assumption that if a location search doesn't have contain a numeric value that it's a regional or city-wide search
                if ($location = $this->searchLocation->getByName($params['search_location'])) {
                    $this->applyRegionalParams($location);
                } elseif (strpos($params['search_location'], ',') !== false) {
                    // If a comma is present, and did not match directly, parameters came from a job alert search 
                    list($suburb, $state) = explode(',', $params['search_location']);
                    if ($location = $this->location->findBySuburbCombination(trim($suburb), trim($state))) {
                        $this->setPostcode($location->PostalCode)->setRadius(30);
                        $this->setLocationId($location->MapLocationID);
                        $this->locationName = $location->City;
                    }
                } else {
                    // Not a complete regional match nor does it match a job alert search
                    // Check for partial match for suburb name; regional first, then specific
                    $location = $params['search_location'];
                    $match = $this->searchLocation->findByName($location, 1);

                    if (count($match) > 0) {
                        $this->applyRegionalParams($match[0]);
                    } elseif ($match = $this->location->findBySuburb($location)) {
                        $this->setPostcode($match->PostalCode)->setRadius(30);
                        $this->setLocationId($match->MapLocationID);
                        $this->locationName = $match->City;
                    }
                }
            } else if (count($location) >= 3) {
                $postcode = array_pop($location);
                $state = array_pop($location);
                $suburb = implode(' ', $location);
                $postcode = (strlen($postcode) == 4) ? $postcode : '0' . $postcode;
                if (is_numeric($postcode)) {
                    if ($location = $this->location->findBySuburbCombination($suburb, $state, $postcode)) {
                        $this->setPostcode($location->PostalCode)->setRadius(30);
                        $this->setLocationId($location->MapLocationID);
                        $this->locationName = $location->City;
                    }
                }
            } else if ($hasPostcode) {
                $postcode = array_pop($location);
                $postcode = (strlen($postcode) == 4) ? $postcode : '0' . $postcode;
                if ($location = $this->location->findByPostcode($postcode)) {
                    $this->setPostcode($location->PostalCode)->setRadius(30);
                    $this->setLocationId($location->MapLocationID);
                    $this->locationName = $location->City;
                }
            }
        }
        if ($jobType != 'casual') {
            if (isset($params['search_salary_min']) && ! empty($params['search_salary_min'])) {
                $this->setSalaryMin((int) $params['search_salary_min']);
            }
            if (isset($params['search_salary_max']) && ! empty($params['search_salary_max'])) {
                $this->setSalaryMax((int) $params['search_salary_max']);
            }
        }
        return $this;
    }

    public function setLocationId($locationId) {
    	$this->locationId = '&lid=' . $locationId;
    	return $this;
    }

    public function setCategory($category) {
        $this->category = '&jcat=' . str_replace(' ', '+', $category);
        return $this;
    }

    public function setLimit($limit) {
    	$this->limit = '&pp=' . $limit;
    	return $this;
    }

    public function setSalaryMin($min) {
        $this->salaryMin = '&salmin=' . $min;
        $this->salaryType = '&saltyp=' . self::SALARY_ANNUAL;
        return $this;
    }

    public function setSalaryMax($max) {
        $this->salaryMax = '&salmax=' . $max;
        $this->salaryType = '&saltyp=' . self::SALARY_ANNUAL;
        return $this;
    }

    public function setJobType($type) {
        $this->jobType = '&jtyp=' . $type;
        return $this;
    }

    public function setJobStatus($status) {
        $this->jobStatus = '&jsta=' . $status;
        return $this;
    }

    public function setQuery($query) {
        $this->query = '&q=' . str_replace(' ', '+', $query);
        return $this;
    }

    public function setQueryBehaviour($behaviour) {
        $this->queryBehaviour = '&qb=' . $behaviour;
        return $this;
    }

    public function setPostcode($postcode) {
        $this->postcode = '&zip=' . $postcode;
        return $this;
    }

    public function setRadius($radius) {
        $this->radius = '&rad=' . $radius;
        return $this;
    }

    public function setLocationString($string) {
        $this->locationString = $string;
        return $this;
    }

    public function setWhere($where) {
        $this->where = '&where=' . $where;
        return $this;
    }

    public function setPage($page) {
        $this->page = '&page=' . $page;
        return $this;
    }

    public function setPageSize($pageSize) {
        $this->pageSize = '&pagesize=' . $pageSize;
        return $this;
    }

    public function setCompanyCode($companyCode) {
        $this->companyCode = '&co=' . $companyCode;
        return $this;
    }

    public function setCompanyName($companyName) {
        $this->companyName = '&cn=' . $companyName;
        return $this;
    }

    public function getLocationId() {
        $locationId = explode('=', $this->locationId);
        return (isset($locationId[1])) ? explode(',', $locationId[1]) : $locationId[0];
    }

    public function getLocationName() {
        return $this->locationName;
    }

    public function hasSearched($params) {
        if (is_array($params)) {
            foreach ($params as $key => $search) {
                if (! empty($search) && $key != 'location_id') {
                    return true;
                }
            }
        }
        return false;
    }

    public function getSalaryRanges() {
        return [
            'min' => [0, 30000, 50000, 70000, 90000, 110000, 140000, 200000], 
            'max' => [30000, 50000, 70000, 90000, 110000, 140000, 200000, 300000]
        ];
    }

    public function getJobTypes() {
        return [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'casual' => 'Casual'
        ];
    }

    public function getFilters(array $params,   $categories) {
        $filters = [];
        foreach ($params as $name => $value) {
            if (empty($value)) {
                continue;
            }
            switch ($name) {
                case 'search_location':
                case 'search_keywords':
                    $filters[$name] = $value;
                break;
                case 'search_job_type':
                    $filters[$name] = ucwords(str_replace('-', ' ', $value));
                break;
                case 'search_category':
                    foreach ($categories as $category) {
                        if ($value == $category->code) {
                            $filters[$name] = $category->title;

                            break;
                        }
                    }
                break;
                case 'search_salary_min':
                    $filters[$name] = 'Min Salary: $' . $value;
                break;
                case 'search_salary_max':
                    $filters[$name] = 'Max Salary: $' . $value;
                break;
                case 'search_company':
                    if (isset($params['search_company_name'])) {
                        $filters[$name] = $params['search_company_name'];
                    } else {
                        $filters[$name] = 'Company Code: ' . $value;
                    }
                break;
                case 'search_company_name':
                    $filters[$name] = $params['search_company_name'];
                break;
            }
        }
        return $filters;
    }

    public function getQuery($cat = 'c1') {
        $query = $this->url . $this->cy . $this->cat[$cat] . $this->locationId . $this->category;
        $query .= $this->salaryMin . $this->salaryMax . $this->salaryType . $this->jobType;
        $query .= $this->jobStatus . $this->query . $this->queryBehaviour . $this->postcode;
        $query .= $this->radius . $this->locationString . $this->companyCode . $this->exwh;
        $query .= $this->companyName . $this->page . $this->pageSize . $this->where;
        $query .= $this->sort . $this->limit;
        return $query;
    }

    protected function applyRegionalParams($location) {
        $this->locationName = (! empty($location->google_location)) ? ucwords($location->google_location) : $location->name;
        if (! empty($location->lid)) {
            $this->setLocationId($location->lid);
        } else if (! empty($location->url)) {
            $this->setLocationString($location->url);
            if (strpos($location->url, '&lid=') !== false) {
                $url = explode('&', $location->url);
                $lids = [];
                foreach ($url as $part) {
                    if (empty($part) || strpos($part, '=') === false) {
                        continue;
                    }
                    list($name, $val) = explode('=', $part);
                    if ($name == 'lid') {
                        $lids[] = $val;
                    }
                }
                if (! empty($lids)) {
                    $this->setLocationId(implode(',', $lids));
                }
            }
        }
        if (! empty($location->filter_state)) {
            $this->postSearchFilter = array('states' => explode(',', $location->filter_state));
        }
    }
    
    public function getAllCateogry() {
        $query = DB::table('category')->orderBy('title', 'ASC')->select('category.*');
        return $query->get();
    }
}
