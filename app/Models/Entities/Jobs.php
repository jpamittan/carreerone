<?php
namespace App\Models\Entities;
use DB;

class Jobs extends BaseModel {
    protected $table = 'ins_jobs';
    protected $fillable = ['jobid'];

    public static function findByCrmJobid($jobid){
    	return DB::table('ins_jobs')->where('jobid', '=', $jobid)
    	->select('id')->first();
    }

    public static function findByCrmid($job_id){
    	return DB::table('ins_jobs')->where('id', '=', $job_id)
    	->select('jobid')->first();
    }
}
