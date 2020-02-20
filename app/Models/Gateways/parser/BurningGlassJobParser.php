<?php
namespace Application\Models\Gateways\Parser;

use Application\Models\Entities\BurningGlassJob;
use Application\Models\Entities\RejuvenatorJob;
use Application\Models\Gateways\SAXParserGateway;
use DB;
use ConfigProxy, Config;

/**
 * Extends SAX parser gateway class
 */
class BurningGlassJobParser extends SAXParserGateway
{
    protected $job;
    protected $started;
    protected $added = 0;
    protected $duplicate = 0;
    protected $rejuvenator_duplicate = 0;
    protected $deleted = 0;
    protected $excluded_by_company = 0;
    protected $exclude_company_domains = array();
    protected $exclude_company_names = array();
    protected $insert_cache_limit = 10;
    protected $insert_cache = array();
    protected $insert_date = null;
    protected $delete_cache_limit = 10;
    protected $delete_cache = array();
    protected $default_row = array(
        'canon_city' => '',
        'canon_employer' => '',
        'canon_country' => '',
        'domain' => '',
        'email' => '',
        'job_date' => '',
        'job_text' => '',
        'bg_job_id' => '',
        'job_title' => '',
        'apply_url' => '',
        'latitude' => '',
        'longitude' => '',
        'phone' => '',
        'postal_code' => '',
        'posting_html' => '',
        'canon_state' => '',
        'out_file_number' => '',
        'apply_url_md5' => '',
        'url_slug' => '',
        'created_at' => '',
        'updated_at' => ''
    );

    public function __construct()
    {
        $this->exclude_company_domains = ConfigProxy::get('careerone.burning_glass.output_company_exclude.domains');
        $this->exclude_company_names = ConfigProxy::get('careerone.burning_glass.output_company_exclude.names');
        $this->insert_date = date('Y-m-d H:i:s');
        $this->insert_cache_limit = Config::get('careerone.burning_glass.import_insert_limit');
        $this->delete_cache_limit = Config::get('careerone.burning_glass.import_delete_limit');
        $this->default_row['created_at'] = $this->insert_date;
        $this->default_row['updated_at'] = $this->insert_date;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getStats() {
        return array(
            'type' => $this->type,
            'added' => $this->added,
            'duplicate' => $this->duplicate,
            'rejuvenator_duplicate' => $this->rejuvenator_duplicate,
            'excluded_by_company' => $this->excluded_by_company,
            'deleted' => $this->deleted
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
        switch ($tag_name) {
            case 'Job':
                $this->job = $this->default_row;
                $this->job['out_file_number'] = rand(0, Config::get('careerone.burning_glass.output_file_max'));
                $this->started = true;
                break;
            case 'row':
                $this->job = $this->default_row;
                $this->started = true;
                break;
        }
        $this->currentTag = $tag_name;
    }

    /**
     * Implemented function to set up task when a xml tag ends
     * @param $parser
     * @param string @tag_name
     * @return void
     */
    public function endTag($parser, $tag_name)
    {
        if ($tag_name == 'Job' || $tag_name == 'row') {

            if (
                ($this->job['domain'] == '' || empty(preg_grep('/' . preg_quote($this->job['domain'], '/') . '/', $this->exclude_company_domains))) &&
                ($this->job['apply_url'] == '' || empty(preg_grep('/' . preg_quote($this->job['apply_url'], '/') . '/', $this->exclude_company_domains))) &&
                ($this->job['email'] == '' || empty(preg_grep('/' . preg_quote($this->job['email'], '/') . '/', $this->exclude_company_domains))) &&
                ($this->job['canon_employer'] == '' || empty(preg_grep('/' . preg_quote($this->job['canon_employer'], '/') . '/', $this->exclude_company_names)))
            ) {

                $jobObj = BurningGlassJob::where('bg_job_id', '=', $this->job['bg_job_id'])->first();

                if ($this->type == 'add') {
                    if ($jobObj) {
                        $this->duplicate ++;
                    } else {
                        $this->job['apply_url_md5'] = hex2bin(md5($this->job['apply_url']));

                        if (RejuvenatorJob::where('apply_url_md5', '=', $this->job['apply_url_md5'])->exists()) {
                            $this->rejuvenator_duplicate ++;
                        } else {
                            $this->added ++;
                            $this->job['url_slug'] = \Str::slug($this->job['job_title'] . " " . $this->job['bg_job_id']);
                            //$this->job->save();
                            $this->insert_cache[] = $this->job;
                        }
                    }
                } else {
                    if ($jobObj) {
                        $this->deleted ++;
                        $this->delete_cache[] = $jobObj->id;
                    }
                }

            } else {
                $this->excluded_by_company ++;
            }

            if (count($this->insert_cache) >= $this->insert_cache_limit) {
                $this->processInsertCache();
            }

            if (count($this->delete_cache) >= $this->delete_cache_limit) {
                $this->processDeleteCache();
            }

        }

        $this->currentTag = '';
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
        if ($this->currentTag != '' && $this->started) {
            switch ($this->currentTag) {
                case 'CanonCity':
                    $this->job['canon_city'] .= $data;
                    break;
                case 'CanonEmployer':
                    $this->job['canon_employer'] .= $data;
                    break;
                case 'CanonCountry':
                    $this->job['canon_country'] .= $data;
                    break;
                case 'Domain':
                    $this->job['domain'] .= $data;
                    break;
                case 'Email':
                    $this->job['email'] .= $data;
                    break;
                case 'JobDate':
                    $this->job['job_date'] .= $data;
                    break;
                case 'JobText':
                    $this->job['job_text'] .= $data;
                    break;
                case 'JobID':
                    $this->job['bg_job_id'] .= $data;
                    break;
                case 'JobTitle':
                    $this->job['job_title'] .= $data;
                    break;
                case 'JobURL':
                    $this->job['apply_url'] .= $data;
                    break;
                case 'Latitude':
                    $this->job['latitude'] .= $data;
                    break;
                case 'Longitude':
                    $this->job['longitude'] .= $data;
                    break;
                case 'Phone':
                    $this->job['phone'] .= $data;
                    break;
                case 'PostalCode':
                    $this->job['postal_code'] .= $data;
                    break;
                case 'PostingHTML':
                    $this->job['posting_html'] .= $data;
                    break;
                case 'CanonState':
                    $this->job['canon_state'] .= $data;
                    break;
            }
        }
    }

    public function processInsertCache()
    {
        if (count($this->insert_cache)) {
            $inserts = [];

            foreach ($this->insert_cache as $key => $row) {
                foreach ($row as $row_key => $value) {
                    $row[$row_key] = DB::getPdo()->quote($value);
                }
                $inserts[] = "(" . implode(", ", $row) . ")";
            }

            $sql = "INSERT INTO " . DB::getTablePrefix() . "burning_glass_job (" . implode(', ', array_keys($this->insert_cache[0])) . ") VALUES " . implode(", ", $inserts);
            try {
            	DB::statement($sql);
            } catch (\Exception $e) {
            	$this->added --;
            	$this->deleted ++;
            }
            //BurningGlassJob::insert($this->insert_cache);
            unset($this->insert_cache);
            $this->insert_cache = [];
        }
    }

    public function processDeleteCache()
    {
        if (count($this->delete_cache)) {
            BurningGlassJob::destroy($this->delete_cache);
            unset($this->delete_cache);
            $this->delete_cache = [];
        }
    }
}
