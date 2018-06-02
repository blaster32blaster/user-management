<?php

namespace App\Services;

use App\LinkedSocialAccount;
use GuzzleHttp\Client as HttpClient;
use App\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Facades\Socialite;

class OauthService
{

    public $auth_params;

    public $referrer;

    public $accessToken;

    public $provider;

    public $client;

    public $oauthClient;

    public $internal = true;

    public $externalUser;

    public $internalUser;

    protected $account;

    public function checkUserAccessLocal()
    {

    }

    public function checkUserAccessProvider()
    {

    }

    public function setAccessToken()
    {
        if ($this->auth_params[0]) {
            $this->accessToken = $this->auth_params[0];
            return true;
        }
        return false;
    }

    public function setProvider()
    {
        if (!empty($this->auth_params[1]) && config('services.' .$this->auth_params[1])) {
            $this->provider = $this->auth_params[1];
            return true;
        }
        return false;
    }

    public function setClientInfo()
    {
        // get the default client information
        $this->client = config('acceptedoauthclients.clients.' . $this->referrer)
            ? config('acceptedoauthclients.clients.' . $this->referrer)
            : '';
//        $this->client = config('acceptedoauthclients.'. $this->referrer)
//            ? config('acceptedoauthclients.'. $this->referrer)
//            : '';

        //make sure that the config value is set properly
        if (empty($this->client['client_id'])) {
            return response(json_encode(false));
        }
        return true;
    }

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

    public function makeExternalOauthRequest()
    {
        $this->account = '';
        $user = '';

        $this->internalUser = json_decode($this->getInternalUser());
        $this->account = LinkedSocialAccount::where('user_id', $this->internalUser->id)
            ->where('provider_name', $this->provider)->first();

        return $this->makeProviderRequest() ? true : false;
    }

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

    public function renewOauthConnection()
    {
//@todo : doing this in the frontend now
return false;
//        if (!$this->removeLinkedSocialAccount()) {
//            return false;
//        }


//        @todo : working here, need to take internal request to forward to provider

//        redirect()->route(config('acceptedoauthclients.thisUrl') . '/login/' . $this->provider)
        redirect()->route('oauth-login', ['provider' => $this->provider])
            ->with('referer', $this->referrer);

//        session(['referrer' => $this->referrer]);
//        $socialite = Socialite::driver($this->provider)->redirect();
//        if ($socialite) {
//            return $socialite;
//        }
//        return response(' Bad Referrer', 403);

//        $headers = [
//            'Accept' => 'application/json',
//            'Callback' => config('acceptedoauthclients.thisUrl'),
//            'Referer' => $this->referrer
//
//        ];
//        ob_start();
//        header("Location:" . config('acceptedoauthclients.thisUrl') . '/login/' . $this->provider, true);
//        ob_end_flush();
//        die();

//        $client = new HttpClient();
//        $url = env('APP_URL') . '/login/'. $this->provider;
//        $response = $client
//            ->request('GET', $url, [
//                'headers' => $headers
//            ])->getBody()->getContents();
//
//        if ($response) {
//            return $response;
//        }
//        return '';
    }

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