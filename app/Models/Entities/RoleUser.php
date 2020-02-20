<?php
namespace App\Models\Entities;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class RoleUser extends Eloquent {
    protected $table = 'role_user';
    public $timestamps = false;
}