<?php

namespace App\Models\Entities;

class CaseManager extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'ins_system_users';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees() 
        return $this->hasMany('App\Models\Entities\Employee', 'ownerid', 'systemuserid');
    }
}