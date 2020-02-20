<?php

namespace App\Models\Repositories;

use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Entities\JobMatchCandidateCategory;
use App\Models\Entities\JobMatch;
use App\Models\Entities\Jobs;
use App\Models\Entities\JobCategory;
use App\Models\Entities\JobFunction;
use App\Models\Entities\JobWorkType;
use App\Models\Entities\AgencyBranch;
use App\Models\Entities\AgencyDetails;
use App\Models\Entities\FileProcessingStatus;
use Carbon\Carbon;
use DB, Config, Redirect, Session, URL, Validator, View;

class ReadCSVRepository extends RepositoryBase {
    public function postJobs($row) {
        if (!empty($row['jobtitle'])) {
            $category = $row['jobcategory'];
            $function = $row['jobfunction'];
            $emp_status = $row['employmentstatus'];
            $agency_name = $row['agencyname'];
            $agency_branch = $row['agencybranch'];
            $category_id = $this->postCategory($category);
            $function_id = $this->postFunction($function);
            $emp_status_id = $this->postEmpStatus($emp_status);
            $agency_branch_id = $this->postAgencyDetails($agency_name,$agency_branch);
            $state = $this->state($row);
            $approverd_date = Carbon::createFromFormat('d/m/Y',$row['approveddate']);
            $jobs = new Jobs();
            $jobs->job_title = $row['jobtitle'];
            $jobs->agency_branch_id = $agency_branch_id;
            $jobs->vacancy_reference_id = $row['vacancyreference'];
            $jobs->appreoved_date = $approverd_date->toDateString();
            $jobs->suburb = $row['region'];
            $jobs->state = !empty($state)? $state->state:null;
            $jobs->salary_package = $row['salarypackage'];
            $jobs->salary_from = $row['salaryfrom'];
            $jobs->salary_to = $row['salaryto'];
            $jobs->position_description = $row['positiondescription'];
            $jobs->selection_criteria = $row['selectioncriteria'];
            $jobs->enquirey_name = $row['enquiriesname'];
            $jobs->enquire_number = $row['enquiriesnumber'];
            $jobs->prepared_by_name = $row['preparedbyname'];
            $jobs->prepared_by_number = $row['preparedbynumber'];
            $jobs->prepared_by_email = $row['preparedbyemail'];
            $jobs->deadline_date = date('Y-m-d', strtotime($row['deadline_date']));
            $jobs->cluster_only = $row['clusteronly'];
            $jobs->agency_only = $row['agencyonly'];
            $jobs->job_type = $row['jobtype'];
            $jobs->job_function_id =  $function_id;
            $jobs->job_category_id = $category_id;
            $jobs->employment_status_id = $emp_status_id;
            $jobs->save();
        }
        return true;
    }

    public function postFunction($function) {
        $function = trim($function);
        if (!empty($function)) {
            $job_func_id = DB::table('ins_job_function')->where('name' ,'=', $function)->select('id')->first();
           if (empty($job_func_id)) {
                $jobFunction = new JobFunction();
                $jobFunction->name = $function;
                $jobFunction->save();
                $function_id = $jobFunction->id;
                return $function_id;
           } else {
                return $job_func_id->id;
           }
        } else {
            return null;
        }
    }

    public function postEmpStatus($emp_status) {
        $emp_status = trim($emp_status);
        if (!empty($emp_status)) {
            $job_emp_id = DB::table('ins_work_type')->where('work_type' ,'=', $emp_status)->select('id')->first();
           if (empty($job_emp_id)) {
                $jobEmpStatus = new JobWorkType();
                $jobEmpStatus->work_type = $emp_status;
                $jobEmpStatus->save();
                $emp_status_id = $jobEmpStatus->id;
                return $emp_status_id;
           } else {
                return $job_emp_id->id;
           }
        } else {
            return 0;
        }
    }

    public function postCategory($category) {
        $category = trim($category);
        if (!empty($category)) {
            $cate = DB::table('ins_job_category')->where('category_name' ,'=', $category)->select('id')->first();
            if (empty($cate)) {
                $jobCategory = new JobCategory();
                $jobCategory->category_name = $category;
                $jobCategory->save();
                $category_id = $jobCategory->id;
                return $category_id;
            } else {
                return $cate->id;
            }
        } else {
            return 0;
        }
    }

    public function postAgencyDetails($agency_name, $agency_branch) {
        $agency_name = trim($agency_name);
        if (!empty($agency_name)) {
            $agency_id = DB::table('ins_agency_details')->where('is_active' ,'1')->where('agency_name' ,'=', $agency_name)->select('id')->first(); 
            if (empty($agency_id)) {
                $agency_details = new AgencyDetails();
                $agency_details->agency_name = $agency_name;
                $agency_details->save();
                $agency_id = $agency_details->id;
                $agency_branch_location = new AgencyBranch();
                $agency_branch_location->location_name = $agency_branch;
                $agency_branch_location->agency_id = $agency_id->id;
                $agency_branch_location->save();
                $ag_branch_id = $agency_branch_location->id;
                return $ag_branch_id;
            } else {
                $ag_branch = DB::table('ins_agency_branch')
                ->where('location_name' ,'=', $agency_branch)
                ->where('agency_id' ,'=', $agency_id->id)
                ->select('id')
                ->first();                                          
                if (empty($ag_branch)) {
                    $agency_branch_location = new AgencyBranch();
                    $agency_branch_location->location_name = $agency_branch;
                    $agency_branch_location->agency_id = $agency_id->id;
                    $agency_branch_location->save();
                    $ag_branch_id = $agency_branch_location->id;
                    return $ag_branch_id;
                }
                return $ag_branch->id;
            }
        } else {
            return 0;
        }
    }

    public function state($row) {
        $location = $row['location'];
        $location = explode(" ", $location);
        $loca = isset($location[0])?$location[0]:'';
        if (!empty($loca)) {
            $state =  DB::table('location')
            ->where('city', 'like', '%'.$loca)
            ->select('state')
            ->groupBy('state')
            ->first();
            return $state;
        } else {
            $state = $location[0];
            return $state;
        }
    }

    public function postFileProcessingStatus($filename, $value) {
        $fileprocessing = new FileProcessingStatus();
        $fileprocessing->file_name = $filename;
        $fileprocessing->process_start = Carbon::now();
        $fileprocessing->process_status = $value;
        $fileprocessing->save();
        $file_proce_id = $fileprocessing->id;
        return $file_proce_id;
    }

    public function postFileProcessingStatusUpdate($filename, $value, $file_proc_id) {
        $fileprocessing = new FileProcessingStatus();
        $fileprocessing1 = $fileprocessing->find($file_proc_id);
        $fileprocessing1->process_status = $value;
        $fileprocessing1->save();
    }

    public function postFileProcessingStatusUpdateFinal($filename, $value, $file_proc_id) {
        $fileprocessing = new FileProcessingStatus();
        $fileprocessing1 = $fileprocessing->find($file_proc_id);
        $fileprocessing1->process_status = $value;
        $fileprocessing1->process_end = Carbon::now();
        $fileprocessing1->save();
    }

    public function postFileProcessingError($filename, $value, $file_proc_id, $e) {
        $fileprocessing = new FileProcessingStatus();
        $fileprocessing1 = $fileprocessing->find($file_proc_id);
        $fileprocessing1->process_status = $value;
        $fileprocessing1->process_message = $e;
        $fileprocessing1->process_end = Carbon::now();
        $fileprocessing1->save();
    }
}
