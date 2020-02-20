<?php

namespace App\Models\Services;

use App\Libraries\Sanitize;
use App\Models\Containers\DataObject;
use App\Models\Repositories\RepositoryBase;
use App\Models\Entities\User;
use App\Models\Entities\CandidateSkillMatch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use DB, Config, Format, Redirect, Session, URL, Validator, View;

class ExpiredJobService {
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
      $this->repo = app()->make('App\Models\Repositories\ExpiredJobRepository');
  }

  public function expiredJob(){
    $expiredJobs = $this->repo->chkExpiredJobs();
    foreach($expiredJobs as $object) {
      $this->repo->updateExpiredJob($object); //Update is_expired
      $this->repo->sendExpiredEmail($object); //Send email to recruiter
    }
  }
}
