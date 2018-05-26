<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkedSocialAccount extends Model
{

    protected $fillable =
        [
            'provider_name',
            'provider_id',
            'provider_token',
            'token_secret'
        ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
