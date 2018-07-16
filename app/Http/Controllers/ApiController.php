<?php

namespace App\Http\Controllers;

use App\Services\RoleScopeService;
use App\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Client;
use App\Services\OauthService;
use Illuminate\Foundation\Application;
use Laravel\Passport\Http\Controllers\ClientController;
use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class ApiController extends Controller
{
    /**
     * @var OauthService
     */
    public $oauthServices;

    /**
     * the app
     *
     * @var Application
     */
    protected $app;

    /**
     * @var RoleScopeService
     */
    protected $roleScopeServices;

    /**
     * Constructor
     *
     * @param Application $app        The app instance.
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        $this->roleScopeServices = resolve(RoleScopeService::class);
        $this->roleScopeServices->user = $user;

        //add user to the request
        if ($user) {
            $request->merge(['user' => $user]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $this->roleScopeServices->clients = app(ClientController::class)->forUser($request);

            foreach ($this->roleScopeServices->clients as $client) {
                    if ($this->roleScopeServices->hasTheRole('client.admin', $client->id)) {
                        $client->admin = true;
                    }
            }
        }


        return $this->roleScopeServices->clients;
    }

    /**
     * Handle password grants
     *
     * @param ServerRequestInterface $request
     * @return mixed
     * @throws GuzzleException
     */
    public function passwordGrantProxy(ServerRequestInterface $request)
    {
        // set oauthservices
        $this->oauthServices = new OauthService();

        // fetch the referrer, whitelisting
        $this->oauthServices->referrer
            = $request->getServerParams()['HTTP_REFERER'];
        $args = [];

        //set the client
        if (!$this->oauthServices->setClientInfo()) {
            return response(json_encode(false));
        }

        // check whether we are getting a new token or refreshing
        //this signifies a refresh
        if (isset($request->getParsedBody()['refresh_token'])) {
            $args = [
                'refresh_token' => $request->getParsedBody()['refresh_token'],
                ];
            $client['grant_type'] = "refresh_token";
        }

        $username = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];

        //this is for granting new access/refresh tokens
        if (isset($username) && isset($password)) {
            $args = [
                'username' => $username,
                'password' => $password
            ];
        }

        try {
            //set user based off of passed in email
            $this->oauthServices->setUserByEmail($username);
            //retire existing tokens
            $this->oauthServices->retireExistingTokens();

            //fetch the tokens
            $tokens = app(AccessTokenController::class)
                ->issueToken($request->withParsedBody(array_merge(
                    $args,
                    $this->oauthServices->client)));

            //parse out the body containing tokens and expiry
            $parsed = json_decode($tokens->getContent());

        // make internal request with the new token to associate with user for parsing
        $this->oauthServices->accessToken = $parsed->access_token;
        if (!$this->oauthServices->makeInternalOauthRequest()) {
            return response(json_encode(
                'Not Authorized'
            ), 403);
        }

        // set the user instance from the returned user data
        $this->oauthServices->internalUser = User::find($this->oauthServices->internalUser['id']);

//        @todo : here or in the below block we want to get the user to update scopes via roles
//            @todo : roles in place, needs to be built out

        // set the users access token, for updating data
        if ($this->oauthServices->setAccessTokenInstance()) {
            $this->oauthServices->accessToken->name = 'internal';
            $this->oauthServices->accessToken->save();
        }

            //return just the refresh token
            return response(json_encode([
                'refresh_token' => $parsed->refresh_token,
                'access_token' => $parsed->access_token,
                'token_expiry' => $parsed->expires_in]));
        } catch (\Exception $e) {
            return response(json_encode(
                'Not Authorized'
            ), 403);
        }
    }

    /**
     * Handle checking if a user has access to a resource
     *
     * @param ServerRequestInterface $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

        // make a request to the external oauth provider to ensure account is good
        if (!$this->oauthServices->internal) {

            if (!$this->oauthServices->makeExternalOauthRequest()) {

                if (!$this->oauthServices->renewOauthConnection()) {
                    return response(json_encode('renew'), 202);
                }
            }
            return response(json_encode(true));
        }

        return response(json_encode(false));
    }

    public function createClient(Request $request)
    {
        $user = Auth::user();

        $this->roleScopeServices = resolve(RoleScopeService::class);
        $this->roleScopeServices->user = $user;

        //add user to the request
        if ($user) {
            $request->merge(['user' => $user]);
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            //hijack the store new client functionality
            $storeResponse = app(ClientController::class)->store($request);

            // if storing returns properly, give the user the client admin role
            if ($storeResponse->id) {
                $this->roleScopeServices->client = Client::find($storeResponse->id);
                $this->roleScopeServices->setUserAsClientAdmin();

                return $storeResponse;
            }
        }

    }
}
