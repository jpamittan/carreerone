<?php
namespace App\Models\Entities;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Crypt;

class BaseModel extends Eloquent {
    protected $encrypt = [];
}
