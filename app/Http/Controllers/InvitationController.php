<?php

namespace App\Http\Controllers;

use App\Invitation;
use App\User;
use Illuminate\Http\Request;
use App\Services\InvitationServices;
use App\Http\Requests\InvitationAcceptRequest;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    /**
     * @var InvitationServices
     */
    public $invitationServices;

    /**
     * @var Invitation
     */
    protected $invitation;

    public function acceptInvitation(InvitationAcceptRequest $request, $token)
    {
        //set service
        $this->invitationServices = resolve(InvitationServices::class);

        if (!$this->invitationServices->setCurrentInvitation($token)) {
            return response(
                $this->invitationServices->responseMessage,
                404
            );
        }

        //find user and login
        if (!$this->invitationServices->setUserFromInvitation()) {
            return response(
                $this->invitationServices->responseMessage,
                404
            );
        }

        if (!$this->invitationServices->verifyInvitation()) {
            return view('invalid-invite')->with('user_id', $this->invitationServices->user->id);
        }


        if (!$this->invitationServices->handleInvitationAcceptance()) {
            return response(
                $this->invitationServices->responseMessage,
                404
            );
        }

        // redirect
        return redirect('home');
    }

    public function reinvite(Request $request)
    {
        $sent = true;
        $this->invitationServices = resolve(InvitationServices::class);

        $this->invitationServices->user = User::find($request['userId']);

        if (!$this->invitationServices->invalidateUserInvitations()) {
            return view('invalid-invite');
        }

        if (!$this->invitationServices->createNewInvitation()) {
            return view('invalid-invite');
        }

        if (!$this->invitationServices->sendInvitationEmail()) {
            return view('invalid-invite');
        }

        $this->invitationServices->invitation->status = 'pending';
        $this->invitationServices->invitation->save();

        return view('invalid-invite')->with('sent', $sent);
    }
}
