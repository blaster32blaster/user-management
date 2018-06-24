<?php

namespace App\Http\Controllers\Auth;

use App\Invitation;
use App\Mail\NewUserRegistration;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * The current user
     *
     * @var User
     */
    protected $user;

    /**
     * @var Invitation
     */
    protected $invitation;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function registration(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        $this->user = $user;

        if (!$this->createNewInvitation()) {
            redirect()->back()
                ->withErrors('name', 'Failed to Create Invitation');
        }

        if ($this->sendRegistrationEmail()) {
            $this->invitation->status = 'Pending';
            $this->invitation->save();
        }

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    private function sendRegistrationEmail()
    {
       Mail::to($this->user)->send(new NewUserRegistration($this->invitation));

        if( count(Mail::failures()) > 0 ) {
            return false;
        }
        return true;
    }

    private function createNewInvitation()
    {
        $this->invitation = resolve(Invitation::class);

        $this->invitation->user_id = $this->user->id;
        $this->invitation->invite_token = $this->invitation->generateInviteToken($this->user);
        $this->invitation->status = 'open';

        return $this->invitation->save();
    }
}
