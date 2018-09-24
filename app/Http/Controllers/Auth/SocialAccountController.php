<?php

namespace App\Http\Controllers\Auth;

use App\Services\OauthService;
use App\Services\RoleScopeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class SocialAccountController extends Controller
{
    /**
     * The headers pulled from RQ
     *
     * @var $headers
     */
    protected $headers;

    /**
     * The referrer, used to set session for redirect
     *
     * @var string $referer
     */
    protected $referer = '';

    /**
     * To determine if the return redirect is local or a client
     *
     * @var $returnEnvironment
     */
    protected $returnEnvironment;

    /**
     * The return url
     *
     * @var $callback
     */
    protected $callback;

    /**
     * @var RoleScopeService
     */
    protected $roleScopeServices;

    /**
     * @var OauthService
     */
    protected $oauthServices;

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
     * This will handle the callback from the oauth provider
     *
     * @param SocialAccountService $accountService
     * @param $provider
     * @param ServerRequestInterface $req
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallbackApi(SocialAccountService $accountService, $provider, ServerRequestInterface $req)
    {
        //set the role and scope service and oauth services
        $this->roleScopeServices = resolve(RoleScopeService::class);
        $this->oauthServices = resolve(OauthService::class);

        //set the referrer from session
        $this->oauthServices->referrer = session('referrer');

        //if not set, go home
        if (!$this->oauthServices->referrer) {
            return redirect()->to('/home');
        }

//        now check to ensure that there is config data for the provided referrer
        if (!$this->oauthServices->setClientInfo()) {
            return redirect($this->oauthServices->referrer);
        }

        // wipe session
        session()->forget('referrer');

        try {
            $user = Socialite::with($provider)->user();
        } catch (\Exception $e) {
            return redirect($this->oauthServices->referrer);
        }

        //find or create the user
        $authUser = $accountService->findOrCreate(
            $user,
            $provider
        );

        //set the user on the role scope service
        $this->roleScopeServices->user = $authUser;

        // get or attach user roles
        $this->roleScopeServices->handleRoles();
        $createdToken = $authUser->createToken(json_encode($this->oauthServices->referrer));

//        adjust token expiry and name to match the social provider
        $token = $createdToken->token;
        $token->expires_at =
            Carbon::now()->addDays('1');
        $token->name = $provider;

//            set the access token scopes, based off of the user roles
        $token->scopes =
            $this->roleScopeServices->setScopes(
                $authUser,
                $this->oauthServices->client['client_id']
            );

        $token->save();

        return redirect($this->oauthServices->referrer .
            '?access_token=' . $createdToken->accessToken .
            '&provider=' . $provider);
    }

    /**
     * Determine the referrer status
     *
     * @return bool
     */
    private function checkReferer()
    {
        // check if set
        if (!isset($this->headers['referer'][0]) || empty($this->headers['referer'][0])) {
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
            $clients = (array)config('acceptedoauthclients.clients');

            if (array_key_exists($referrer, $clients)) {
                $this->referer = $clients[$referrer]['url'];
                return true;
            }

            logger()->error('Client and not found');
            return false;
        }
        logger()->error('Local and not true');
        return false;
    }

    /**
     * determine the callback status
     *
     * @return bool
     */
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
