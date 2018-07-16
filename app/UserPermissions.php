<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPermissions extends Model
{
    protected $fillable = [
        'user_id', 'permission_id', 'client_id'
    ];

    protected $table = 'permission_user';

    public $timestamps = true;
}
