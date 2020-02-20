<?php
namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB, Config, Format, Redirect, Session, URL, Validator, View;
use App\Models\Factories\ExternalFileFactory;

class ReadCSVService {

    /**
     * Response object with details
     */
    private $response = null;

    /**
     * Response object with details
     */
    private $repo = null;

    /**
     * ALl class dependencies in the constructor
     */
    public function __construct() {
        $this->repo = app()->make('App\Models\Repositories\ReadCSVRepository');
    }

    public function postCSV($row) {
        $job_id = $this->repo->postJobs($row);
    }

    public function postFileProcessingStatus($filename, $value) {
        $file_proc_id = $this->repo->postFileProcessingStatus($filename,$value);
        return $file_proc_id;
    }

    public function postFileProcessingStatusUpdate($filename, $value, $file_proc_id) {
        $this->repo->postFileProcessingStatusUpdate($filename,$value,$file_proc_id);
    }

    public function postFileProcessingError($filename, $value, $file_proc_id,$e) {
        $this->repo->postFileProcessingError($filename,$value,$file_proc_id,$e);
    }

    public function postFileProcessingStatusUpdateFinal($filename, $value, $file_proc_id) {
        $this->repo->postFileProcessingStatusUpdateFinal($filename,$value,$file_proc_id);
    }
}
