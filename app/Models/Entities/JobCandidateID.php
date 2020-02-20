<?php
namespace App\Models\Entities;

class JobCandidateID extends BaseModel {
    protected $table = 'ins_job_candidate';
    
    public function user() {
    	return $this->belongsTo('App\Models\Entities\User','candidate_id');
    }
    
    public function job() {
    	return $this->belongsTo('App\Models\Entities\Jobs', 'job_id');
    }
}
