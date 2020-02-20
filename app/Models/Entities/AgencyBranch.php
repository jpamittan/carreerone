<?php
namespace App\Models\Entities;

class AgencyBranch extends BaseModel {
    /**
     * @var string
     */
    protected $table = 'ins_agency_branch';

    /**
     * @var array
     */
    protected $fillable = ['agency_id', 'location_name'];
}