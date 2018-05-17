<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\ApiController;
use App\Services\SocialAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Bridge\AccessTokenRepository;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessTokenFactory;
use Laravel\Passport\TokenRepository;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
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
                $socialite = Socialite::driver($provider)->redirect();
                return $socialite;
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

        $createdToken = $authUser->createToken($referrer);
        $token = $createdToken->token;
        $token->expires_at =
            Carbon::now()->addDays('1');
        $token->name = $provider;

        $token->save();

        return redirect($referrer .
            '?access_token=' . $createdToken->accessToken .
            '&provider=' . $provider);
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
