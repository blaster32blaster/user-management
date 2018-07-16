<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
    protected $fillable = [
        'user_id', 'role_id', 'client_id'
    ];

    protected $table = 'role_user';

    public $timestamps = true;
}
