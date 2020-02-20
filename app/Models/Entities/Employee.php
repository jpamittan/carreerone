<?php

namespace App\Models\Entities;

/**
 * Class Employee
 * @package App\Models\Entities
 */
class Employee extends BaseModel {
    protected $table = 'ins_employees';
    protected $fillable = ['employeeid'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caseManager() {
        return $this->belongsTo(
        	'App\Models\Entities\CaseManager',
        	'ownerid',
        	'systemuserid'
        );
    }
}
