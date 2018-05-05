<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\SocialAccountService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\ServerRequestInterface;

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

        if ($referrer || !empty($referrer)) {
            session(['referrer' => $referrer]);

        if ($provider === 'twitter') {
            if (config('acceptedoauthclients.' . $referrer)) {
                $socialite = Socialite::driver($provider)->redirect();
                return $socialite;
            }
        } else {
            if (config('acceptedoauthclients.' . $referrer)) {
//            @todo : the with method isnt working here
                return Socialite::driver($provider)
                    ->with(['hd' => 'example.com'])->redirect();
            }
        }
        return response(' Not Authorized', 403);
        }
    }

    /**
     * @param SocialAccountService $accountService
     * @param $provider
     * @param ServerRequestInterface $req
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallbackApi(SocialAccountService $accountService, $provider, ServerRequestInterface $req)
    {
        $referrer = session('referrer');

        if (!$referrer) {
            return redirect()->to('/home');
        }

        session()->forget('referrer');

        try {
            $user = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect($referrer);
        }

        $authUser = $accountService->findOrCreate(
            $user,
            $provider
        );

        $accessToken = $authUser->createToken($referrer)->accessToken;

        return redirect($referrer . '?access_token=' . $accessToken);
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
     * @param SocialAccountService $accountService
     * @param $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback(SocialAccountService $accountService, $provider)
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
