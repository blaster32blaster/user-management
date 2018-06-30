<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'user_id', 'invite_token', 'status',
    ];

    public function generateInviteToken(User $user)
    {
        $date = new Carbon();
        return $user->id . '_' .
        $date->format('Y-m-d');

    }

    public function validateInviteToken($token)
    {
        $token = decrypt($token);
        $invitation = Invitation::where('invite_token', $token)
            ->where('status', 'pending')->get();
        return $invitation->count() === 1 ? $invitation->first() : null;

    }
}
