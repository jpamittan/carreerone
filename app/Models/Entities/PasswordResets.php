<?php
namespace App\Models\Entities;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\Model as Eloquent;

class PasswordResets extends Eloquent {
    protected $table = 'password_resets';
    public $timestamps = false;
}
