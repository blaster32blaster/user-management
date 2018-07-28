<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use jeremykenedy\LaravelRoles\Models\Role;

class UserRoles extends Model
{
    protected $fillable = [
        'user_id', 'role_id', 'client_id'
    ];

    protected $table = 'role_user';

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
