<?php

namespace App\Services;

use App\LinkedSocialAccount;
use GuzzleHttp\Client as HttpClient;
use App\User;
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
        $this->client = config('acceptedoauthclients.'. $this->referrer)
            ? config('acceptedoauthclients.'. $this->referrer)
            : '';

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
        $account = '';
        $user = '';

        $this->internalUser = json_decode($this->getInternalUser());
        $account = LinkedSocialAccount::where('user_id', $this->internalUser->id)
            ->where('provider_name', $this->provider)->first();

//        @todo : this is where we are... works with github
    $this->externalUser = Socialite::driver($this->provider)->userFromToken($account->provider_token);
        return $this->externalUser;
        if ($this->externalUser) {
            return true;
        }
        return false;
    }

    public function getInternalUser()
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

        if ($response) {
            return $response;
        }
    }
}