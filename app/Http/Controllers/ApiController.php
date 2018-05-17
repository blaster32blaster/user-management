<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\LinkedSocialAccount;
use App\Services\OauthService;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as HttpClient;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Client;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Laravel\Socialite\Facades\Socialite;
use Psr\Http\Message\ServerRequestInterface;

class ApiController extends Controller
{
    /**
     * @var OauthService
     */
    public $oauthServices;

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function passwordGrantProxy(ServerRequestInterface $request)
    {
        // fetch the referrer, whitelisting
        $referer = $request->getServerParams()['HTTP_REFERER'];
        $args = [];

        // set the default client information
        $client = config('acceptedoauthclients.'. $referer)
            ? config('acceptedoauthclients.'. $referer)
            : '';

        // check whether we are getting a new token or refreshing
        //this signifies a refresh
        if (isset($request->getParsedBody()['refresh_token'])) {
            $args = [
                'refresh_token' => $request->getParsedBody()['refresh_token'],
                ];
            $client['grant_type'] = "refresh_token";
        }
        //this is for granting new access/refresh tokens
        if (isset($request->getParsedBody()['username']) && isset($request->getParsedBody()['password'])) {
            $args = [
                'username' => $request->getParsedBody()['username'],
                'password' => $request->getParsedBody()['password']
            ];
        }

        //fetch the tokens
        $tokens =  app(AccessTokenController::class)
            ->issueToken($request->withParsedBody(array_merge(
                $args,
                $client)));

        //parse out the body containing tokens and expiry
        $parsed = json_decode($tokens->getContent());

        //return just the refresh token
        return response(json_encode([
            'refresh_token' => $parsed->refresh_token,
            'access_token' => $parsed->access_token,
            'token_expiry' => $parsed->expires_in]));

    }

    public function authorizationProxy(ServerRequestInterface $request)
    {
        //set the OauthService
        $this->oauthServices = new OauthService();

        // set the auth params
        $this->oauthServices->auth_params = $request->getServerParams()['HTTP_AUTHORIZATION'];
        $this->oauthServices->auth_params
            = explode(',', $this->oauthServices->auth_params);

        //set the referrer
        $this->oauthServices->referrer = $request->getServerParams()['HTTP_ORIGIN'];

        //set the access token
        if (!$this->oauthServices->setAccessToken()) {
            return response(json_encode(false));
        }

        //set the provider
        $this->oauthServices->provider = '';

        if ($this->oauthServices->setProvider()) {
            $this->oauthServices->internal = false;
        }

        //set the client
        if (!$this->oauthServices->setClientInfo()) {
            return response(json_encode(false));
        }
        $this->oauthServices->oauthClient = Client::find($this->oauthServices->client['client_id']);

        //make a request to check local access
        if ($this->oauthServices->internal) {
            if (!$this->oauthServices->makeInternalOauthRequest()) {
                return response(json_encode(false));
            } else {
                return response(json_encode(true));
            }
        }

        if (!$this->oauthServices->internal) {
            return response(json_encode($this->oauthServices->makeExternalOauthRequest()));
            if (!$this->oauthServices->makeExternalOauthRequest()) {
                return response(json_encode(false));
            }
            return response(json_encode(true));
        }

        return response(json_encode('asdasd'));
    }

    public function checkOauthProviderToken($data)
    {
        $user = User::find($data->id);
        $token = $user->token();
        return true;
//        @todo : we need to do an http call out to the provider here
    }
}
