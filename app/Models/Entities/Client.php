<?php
namespace App\Models\Entities;

class Client extends BaseModel {
    protected $table = 'ins_clients';
    protected $fillable = ['clientid'];
}
