<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class SocialAccountController extends Controller
{
    /**
     * Redirect the user to the Social authentication page.
     *
     * @param $provider
     * @param Request $request
     * @return Socialite
     */
    public function redirectToProviderApi($provider, Request $request)
    {
        $headers = $request->headers->all();
        $referrer = $headers['referer'][0];

        if (config('acceptedoauthclients.'. $referrer)) {
//            @todo : the with method isnt working here
            return Socialite::driver($provider)
                ->with(['calling_app' => $referrer])
                ->redirect();

        }
    }

    /**
     * @param \App\SocialAccountsService $accountService
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallbackApi(\App\SocialAccountsService $accountService, $provider)
    {
//        #todo : this needs to be built out and tested
        try {
            $user = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }

        $authUser = $accountService->findOrCreate(
            $user,
            $provider
        );

        auth()->login($authUser, true);

        return redirect()->to('/home');
    }

    /**
     * @param $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
            return Socialite::driver($provider)
                ->redirect();
    }

    /**
     * Obtain the user information
     */
    public function handleProviderCallback(\App\SocialAccountsService $accountService, $provider)
    {

        try {
            $user = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login');
        }

        $authUser = $accountService->findOrCreate(
            $user,
            $provider
        );

        auth()->login($authUser, true);

        return redirect()->to('/home');
    }
}
