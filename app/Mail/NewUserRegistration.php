<?php

namespace App\Mail;

use App\Invitation;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserRegistration extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Invitation
     */
    public $invitation;

    /**
     * @var $inviteToken
     */
    public $inviteToken;

    /**
     * @var $url
     */
    public $url;

    /**
     * @var User
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @param Invitation $invitation
     */
    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
        $this->inviteToken = encrypt($this->invitation->invite_token);
        $this->url = config('acceptedoauthclients.thisUrl') . '/accept-invitation';
        $this->user = User::find($invitation->user_id);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'))
            ->view('emails.registration');
    }
}
