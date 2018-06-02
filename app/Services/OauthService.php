<?php

namespace App\Services;

use App\LinkedSocialAccount;
use Laravel\Passport\Client;
use GuzzleHttp\Client as HttpClient;
use Laravel\Socialite\Facades\Socialite;

class OauthService
{
    /**
     * auth params for internal use
     *
     * @var $auth_params
     */
    public $auth_params;

    /**
     * the referring application
     *
     * @var $referrer
     */
    public $referrer;

    /**
     * The in use access token
     *
     * @var $accessToken
     */
    public $accessToken;

    /**
     * The oauth provider
     *
     * @var $provider
     */
    public $provider;

    /**
     * The oauth client, from config
     *
     * @var $client
     */
    public $client;

    /**
     * The Oauth Client Model
     *
     * @var Client
     */
    public $oauthClient;

    /**
     * To determine if a user is using an external oauth provider
     *
     * @var bool
     */
    public $internal = true;

    /**
     * The user information found within internal sources
     *
     * @var $internalUser
     */
    public $internalUser;

    /**
     * The currently found linked account model
     *
     * @var LinkedSocialAccount
     */
    protected $account;

    /**
     * Set the access token for internal use
     *
     * @return bool
     */
    public function setAccessToken()
    {
        if ($this->auth_params[0]) {
            $this->accessToken = $this->auth_params[0];
            return true;
        }
        return false;
    }

    /**
     * Set the provider
     *
     * @return bool
     */
    public function setProvider()
    {
        if (!empty($this->auth_params[1]) && config('services.' .$this->auth_params[1])) {
            $this->provider = $this->auth_params[1];
            return true;
        }
        return false;
    }

    /**
     * Set the oauth provider client info from config
     *
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function setClientInfo()
    {
        // get the default client information

        $clients = config('acceptedoauthclients.clients');
        $theClient = $clients[$this->referrer];
        if (!empty($theClient)) {
            $this->client = $theClient;
        }

        //make sure that the config value is set properly
        if (empty($this->client['client_id'])) {
            return response(json_encode(false));
        }
        return true;
    }

    /**
     * Handle internal Oauth RQ
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeInternalOauthRequest()
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $this->accessToken
        ];

        $client = new HttpClient();
        $url = env('APP_URL') . '/api/user';
        $response = $client
            ->request('GET', $url, [
                'headers' => $headers
            ])->getBody()->getContents();

        $this->internalUser = (array)json_decode($response);

        if ($this->internalUser['id'] && $this->internalUser['email']) {
            return true;
        }
        return false;
    }

    /**
     * Handle the RQ to external oauth service
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeExternalOauthRequest()
    {
        $this->account = '';
        $user = '';

        $this->internalUser = json_decode($this->getInternalUser());
        $this->account = LinkedSocialAccount::where('user_id', $this->internalUser->id)
            ->where('provider_name', $this->provider)->first();

        return $this->makeProviderRequest() ? true : false;
    }

    /**
     * Get an internal User
     *
     * @return null|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getInternalUser()
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $this->accessToken
        ];

        $client = new HttpClient();
        $url = config('acceptedoauthclients.thisUrl') . '/api/user';

        try {
            $response = $client
                ->request('GET', $url, [
                    'headers' => $headers
                ])->getBody()->getContents();
            if ($response) {
                return $response;
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

    /**
     * Make RQ to oauth provider to ensure account access is still good
     *
     * @return bool
     * @throws \Exception
     */
    public function makeProviderRequest()
    {
        try {
            if ($this->provider === 'twitter') {
                Socialite::driver('twitter')->userFromTokenAndSecret(
                    $this->account->provider_token,
                    $this->account->token_secret
                );
                    } else {
                // for oauth2 providers
                Socialite::driver($this->provider)
                    ->userFromToken($this->account->provider_token);
                }
                return true;
        } catch (\Exception $e) {
            $this->removeLinkedSocialAccount();
            return false;
        }
    }

    /**
     * Originally intended to handle reaquiring provider token here, now doing in front end
     *
     * @return bool
     */
    public function renewOauthConnection()
    {
        //@todo : doing this in the frontend now
        return false;
    }

    /**
     * We want to remove the linked account if its no longer valid
     *
     * @return bool
     * @throws \Exception
     */
    public function removeLinkedSocialAccount()
    {
        if (isset($this->account)) {
            if (!$this->account->delete()) {
                return false;
            }
            return true;
        }
        return true;
    }
}