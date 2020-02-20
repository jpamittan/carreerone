<?php
namespace Application\Models\Gateways\Parser;

use Application\Models\Entities\RejuvenatorJob;
use Application\Models\Entities\RejuvenatorJobLegacy;
use Application\Models\Entities\RejuvenatorJobXcodeImage;
use Application\Models\Gateways\SAXParserGateway;
use DB;
use DateInterval;
use DateTime;

/**
 * Extends SAX parser gateway class
 */
class RejuvenatorJobParser extends SAXParserGateway
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
        $this->openedTag[$tag_name] = $tag_name;
        switch ($tag_name) {
            case 'Job':
                $this->job = new RejuvenatorJob();
                $this->legacy_job = new RejuvenatorJobLegacy();
                $this->xcode_image = new RejuvenatorJobXcodeImage();

                $this->started = true;
                break;
            case "JobPosting":
                $this->job->job_id = array_get($attributes, 'postingId', 0);
                $this->job->bolded = array_get($attributes, 'bold', '') == 'true';
                $this->legacy_job->job_id = array_get($attributes, 'postingId', 0);
                $this->legacy_job->bolded = array_get($attributes, 'bold', '') == 'true';
                break;
            case "JobCategory":
                $this->job->category_id = array_get($attributes, 'monsterId', 0);
                $this->legacy_job->category_id = array_get($attributes, 'monsterId', 0);
                break;
            case "Location":
                if (isset($this->openedTag["Locations"])) {
                    $this->job->lid = array_get($attributes, 'locationId', 0);
                    $this->legacy_job->lid = array_get($attributes, 'locationId', 0);
                }
                break;
            default:
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
        if ($tag_name == 'Job') {
            
            \DB::table('rejuvenator_job')->where('job_id', '=', $this->job->job_id)->delete();
            $this->job->apply_url_md5 = hex2bin(md5($this->job->custom_apply_online_url));
            $this->job->save();
            $this->job = null;

            \DB::connection('careerone')->table('co_rejuvenator_jobs')->where('job_id', '=', $this->legacy_job->job_id)->delete();
            $this->legacy_job->save();
            $this->legacy_job = null;
            
            //write to co_company_xcode_images table
            if(isset($this->xcode_image->xcode)){
                $xcode_image = RejuvenatorJobXcodeImage::where('xcode', '=', $this->xcode_image->xcode)->get();
                if(count($xcode_image) == 0){
                    $this->xcode_image->save();
                }else{
                    foreach($xcode_image as $xc_img){
                        $last_checked = new DateTime($xc_img->last_checked);
                        $interval = new DateInterval("P1D");
                        $last_checked->add($interval);
                        // If record is more than 1 day old, reset the record
                        if ($last_checked <= new DateTime()) {
                            \DB::connection('careerone')->table('co_company_xcode_images')->where('xcode', '=', $this->xcode_image->xcode)->update(Array('has_image' => 0));
                        }
                    }
                }
            }
            $this->xcode_image = null;
            
            $this->started = false;
            $this->records++;
        }
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
                case "JobTitle":
                    $this->job->title = $data;
                    $this->legacy_job->title = $data;
                    break;
                case "JobBody":
                    $this->job->job_body = isset($this->job->job_body) ? $this->job->job_body . $data : $data;
                    $this->legacy_job->job_body = $this->job->job_body;
                    break;
                case "E-mail":
                    if (isset($this->openedTag["JobInformation"]) && isset($this->openedTag["Contact"])) {
                        $this->job->email = $data;
                        $this->legacy_job->email = $data;
                    }
                    break;
                case "CustomApplyOnlineURL":
                    $this->job->custom_apply_online_url = $data;
                    $this->legacy_job->custom_apply_online_url = $data;
                    break;
                case "JobCategories":
                    if (!isset($this->job->category)) {
                        $this->job->category = $data;
                        $this->legacy_job->category = $data;
                    }
                    break;
                case "CompanyXCode":
                    if (!isset($this->job->xcode)) {
                        $this->job->xcode = $data;
                        $this->legacy_job->xcode = $data;
                        $this->xcode_image->xcode = $data;
                    }
                    break;
                case "CountryCode":
                    if (!isset($this->job->country)) {
                        if (isset($this->openedTag["PhysicalAddress"])) {
                            $this->job->country = $data;
                            $this->legacy_job->country = $data;
                        }
                    }
                    break;
                case "PostalCode":
                    if (!isset($this->job->postcode)) {
                        if (isset($this->openedTag["PhysicalAddress"])) {
                            $this->job->postcode = $data;
                            $this->legacy_job->postcode = $data;
                        }
                    }
                    break;
                case "State":
                    if (!isset($this->job->state)) {
                        if (isset($this->openedTag["PhysicalAddress"])) {
                            $this->job->state = $data;
                            $this->legacy_job->state = $data;
                        }
                    }
                    break;
                case "City":
                    if (!isset($this->job->city)) {
                        if (isset($this->openedTag["PhysicalAddress"])) {
                            $this->job->city = $data;
                            $this->legacy_job->city = $data;
                        }
                    }
                    break;
                case "CompanyName":
                    $this->job->company_name = $data;
                    break;
                case "JobCategories":
                    $this->job->categories = isset($this->job->categories) ? $this->job->categories . ',' . $data : $data;
                    $this->legacy_job->categories = isset($this->legacy_job->categories) ? $this->legacy_job->categories . ',' . $data : $data;
                    break;
                case "JobOccupations":
                    $this->job->occupations = isset($this->job->occupations) ? $this->job->occupations . ',' . $data : $data;
                    $this->legacy_job->occupations = isset($this->legacy_job->occupations) ? $this->legacy_job->occupations . ',' . $data : $data;
                    break;
                case "JobPostDate":
                    if (!isset($this->legacy_job->date_posted)) {
                        if (isset($this->openedTag["JobPostingDates"])) {
                            $this->legacy_job->date_posted = $data;
                        }
                    }
                    break;
                case "JobActiveDate":
                    if (!isset($this->legacy_job->date_active)) {
                        if (isset($this->openedTag["JobPostingDates"])) {
                            $this->legacy_job->date_active = $data;
                        }
                    }
                    break;
                case "JobExpireDate":
                    if (!isset($this->legacy_job->date_expire)) {
                        if (isset($this->openedTag["JobPostingDates"])) {
                            $this->legacy_job->date_expire = $data;
                        }
                    }
                    break;
                case "JobModifiedDate":
                    if (!isset($this->legacy_job->date_modified)) {
                        if (isset($this->openedTag["JobPostingDates"])) {
                            $this->legacy_job->date_modified = $data;
                        }
                    }
                    break;
                case "Currency":
                    if (!isset($this->job->currency)) {
                        if (isset($this->openedTag["Salary"])) {
                            $this->job->currency = $data;
                        }
                    }
                break;
                case "SalaryMin":
                    if (!isset($this->job->salary_min)) {
                        if (isset($this->openedTag["Salary"])) {
                            $this->job->salary_min = $data;
                        }
                    }
                break;
                case "SalaryMax":
                    if (!isset($this->job->salary_max)) {
                        if (isset($this->openedTag["Salary"])) {
                            $this->job->salary_max = $data;
                        }
                    }
                break;
                case "SalaryDescription":
                    if (!isset($this->job->salary_description)) {
                        if (isset($this->openedTag["Salary"])) {
                            $this->job->salary_description = $data;
                        }
                    }
                break;
                case "Name":
                    if (!isset($this->job->occupation_name)) {
                        if (isset($this->openedTag["OccupationalClassification"])) {
                            $this->job->occupation_name = $data;
                        }
                    }
                break;
                case "JobStatus":
                    if (!isset($this->job->job_type)) {
                        $this->job->job_type = $data;
                    }
                break;
            }
        }
    }
}
