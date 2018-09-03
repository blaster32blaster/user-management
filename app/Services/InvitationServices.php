<?php

namespace App\Services;

use App\Http\Controllers\Auth\RoleScopeService;
use App\Mail\NewUserRegistration;
use App\User;
use Carbon\Carbon;
use App\Invitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

/**
 * Class InvitationServices
 * @package App\Services
 */
class InvitationServices
{
    /**
     * @var $invitation
     */
    public $invitation;

    /**
     * Invite Token
     *
     * @var $inviteToken
     */
    public $inviteToken;

    /**
     * The Response
     *
     * @var $responseMessage
     */
    public $responseMessage;

    /**
     * @var User
     */
    public $user;

    /**
     * @var RoleScopeService
     */
    protected $roleScopeService;

    public function createNewInvitation()
    {
        $this->invitation = resolve(Invitation::class);
        //set user

        $this->invitation->user_id = $this->user->id;
        $this->invitation->invite_token = $this->invitation->generateInviteToken($this->user);
        $this->invitation->status = 'open';

        return $this->invitation->save();
    }

    public function sendInvitationEmail()
    {
        Mail::to($this->user)->send(new NewUserRegistration($this->invitation));

        if( count(Mail::failures()) > 0 ) {
            return false;
        }
        return true;
    }

    public function invalidateUserInvitations()
    {
        $this->invitation = Invitation::where('user_id', $this->user->id)->get();
        try {
            $this->invitation->each(function ($invite) {
                $invite->status = 'invalid';
                $invite->save();
            });
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function handleInvitationAcceptance()
    {
        //if invitation is valid, register user
        try {
            $this->user->verified = true;
            $this->user->save();
        } catch (\PDOException $e) {
            $this->responseMessage = ' Error fetching User' . $e ;
            return false;
        }

        // and then set the invitation to complete

        $this->invitation->status = 'complete';
        if (!$this->invitation->save()) {
            $this->responseMessage = ' Failure saving Invitation';
            return false;
        }

        $this->responseMessage = ' Successfully handled Invitation';
        return true;
    }

    public function setCurrentInvitation($token)
    {
        //set and decrypt token
        $this->decryptInviteToken($token);

        //find the invitation
        $this->invitation = Invitation::where('invite_token', $this->inviteToken)->get();

        if ($this->invitation->count() > 1) {
            $theReturn = false;
            foreach ($this->invitation as $invite) {
                if ($invite->status === 'pending') {
                    $this->invitation = $invite;
                    $theReturn = true;
                }
            }

            if (!$theReturn) {
                $this->responseMessage = ' No valid Invitations found';
                return false;
            }
            return true;
        }
        if ($this->invitation->count() === 0) {
            $this->responseMessage = ' Invitation not Found';
            return false;
        }

        $this->invitation = $this->invitation->first();
        return true;
    }

    private function decryptInviteToken($token)
    {
        $this->inviteToken = decrypt($token);
    }

    public function verifyInvitation()
    {
        $expirationDate = Carbon::now()->subDays(30);

        if ($this->invitation->created_at->lessThan($expirationDate)) {
            $this->responseMessage = 'Invitation is Expired';
            $this->invitation->status = 'invalid';
            $this->invitation->save();
            return false;
        }

        if ($this->invitation->status !== 'pending') {
            $this->responseMessage = 'Invitation Status not set Properly';
            $this->invitation->status = 'invalid';
            $this->invitation->save();
            return false;
        }

        return true;
    }

    public function setUserFromInvitation() {
        $this->user = User::find($this->invitation->user_id);

        Auth::login($this->user);

        if (!isset(Auth::user()->id)) {
            return false;
        }
        return true;
    }
}