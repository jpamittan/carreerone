<?php
namespace App\Models\Entities;

class JobCategory extends BaseModel {
    protected $table = 'ins_job_category';
    protected $fillable = ['ins_job_category_id'];
    
    public function categoryType() {
    	return $this->belongsTo(
    		'App\Models\Entities\JobCategoryType',
    		'job_category_type_id'
    	);
    }
}
