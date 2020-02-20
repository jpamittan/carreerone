<?php
namespace App\Models\Entities;

class JobCategoryType extends BaseModel {
    protected $table = 'ins_job_category_types';
    protected $fillable = ['ins_job_category_type_id'];

    public function categories() {
    	return $this->hasMany(
    		'App\Models\Entities\JobCategory',
    		'job_category_type_id'
    	);
    }
}
