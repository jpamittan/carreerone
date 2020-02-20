<?php
namespace App\Models\Entities;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent {
    use EntrustUserTrait;

    public function jobcategorytypes() {
    	return $this->belongsToMany(
            'App\Models\Entities\JobCategoryType',
            "ins_user_job_category_types",
            "user_id",
            "job_category_type_id"
        );
    }

    public function joblocations() {
    	return $this->belongsToMany(
            'App\Models\Entities\InsLocation',
            "ins_user_job_locations",
            "user_id",
            "ins_location_id"
        );
    }
    
    public function employee() {
    	return $this->hasOne('App\Models\Entities\Employee', 'user_id');
    }
}
