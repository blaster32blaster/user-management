<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class SocialAccountController extends Controller
{
    protected $headers;

    protected $referer = '';

    protected $returnEnvironment;

    protected $callback;
    /**
     * Redirect the user to the Social authentication page.
     *
     * @param $provider
     * @param Request $request
     * @return Socialite
     */
    public function redirectToProviderApi($provider, Request $request)
    {
        //set headers
        $this->headers = $request->headers->all();

        //check callback to set local
        if (!$this->checkCallback()) {
            logger()->error('Failed Callback Check');
            return response(' Bad Referrer', 403);
        }

        //check referer, is it a local request?
        if (!$this->checkReferer()) {
            logger()->error('Failed Referrer Check');
            return response(' Bad Referrer', 403);
        }

            session(['referrer' => $this->referer]);

                $socialite = Socialite::driver($provider)->redirect();
                if ($socialite) {
                    return $socialite;
                }
        logger()->error('Failed Social Check');
        return response(' Bad Referrer', 403);
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

    private function checkReferer()
    {
        // check if set
        if (!$this->headers['referer'][0] || empty($this->headers['referer'][0])) {
            logger()->error('Referrer Empty');
            return false;
        }
        $referrer = $this->headers['referer'][0];

        //check if we have a query string
        if (strpos($referrer, '?') !== 0) {
            $referrer = strtok($referrer, '?');
        }

        // check if the referer is local
        if ($this->returnEnvironment === 'local') {
            $this->referer = $referrer;
            return true;
        }

        //check accepted clients
        if ($this->returnEnvironment === 'client') {
//            if (config('acceptedoauthclients.' . $referrer)) {
//                $this->referer = $referrer;
//                return true;
//            }
            $ref = config('acceptedoauthclients.'. $referrer)
                ? config('acceptedoauthclients.'. $referrer)
                : null;

            if ($ref !== null) {
                $this->referer = $referrer;
                logger()->error(json_encode($this->referer));
                return true;
            }
            logger()->error(json_encode(config('acceptedoauthclients.http://evenz.online')));
            logger()->error(json_encode(config('acceptedoauthclients.' . $referrer)));
            logger()->error($referrer);
            logger()->error('Client and not found');
            return false;
        }
        logger()->error('Local and not true');
        return false;
    }

    private function checkCallback()
    {
        //check if set
        if (!isset($this->headers['callback'][0])) {
            $this->returnEnvironment = 'client';
            return true;
        }

        //check if local
        if ($this->headers['callback'][0] === config('acceptedoauthclients.thisUrl')) {
            $this->callback = $this->headers['callback'][0];
            $this->returnEnvironment = 'local';
            return true;
        }
        return false;
    }
}
