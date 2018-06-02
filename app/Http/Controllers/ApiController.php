<?php

namespace App\Http\Controllers;

use Laravel\Passport\Client;
use App\Services\OauthService;
use Illuminate\Foundation\Application;
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
     * Constructor
     *
     * @param Application $app        The app instance.
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle password grants
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function passwordGrantProxy(ServerRequestInterface $request)
    {
        // set oauthservices
        $this->oauthServices = new OauthService();

        // fetch the referrer, whitelisting
        $referer = $request->getServerParams()['HTTP_REFERER'];
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

        //this is for granting new access/refresh tokens
        if (isset($request->getParsedBody()['username']) && isset($request->getParsedBody()['password'])) {
            $args = [
                'username' => $request->getParsedBody()['username'],
                'password' => $request->getParsedBody()['password']
            ];
        }

        try {
            //fetch the tokens
            $tokens = app(AccessTokenController::class)
                ->issueToken($request->withParsedBody(array_merge(
                    $args,
                    $this->oauthServices->client)));

            //parse out the body containing tokens and expiry
            $parsed = json_decode($tokens->getContent());

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

}
