<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\User;
use Carbon\Carbon;
use function GuzzleHttp\Promise\queue;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Laravel\Passport\Bridge\AccessToken;
use Laravel\Passport\Bridge\RefreshToken;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Laravel\Passport\Http\Controllers\AuthorizationController;
use Psr\Http\Message\ServerRequestInterface;

class ApiController extends Controller
{
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
        $access_token = $request->getServerParams()['HTTP_AUTHORIZATION'];
        //need to get access token based upon refresh token
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $access_token
        ];

        $client = new Client();
        $response = $client
            ->request('GET', 'http://user.management.local/api/user', [
                'headers' => $headers
            ])->getBody()->getContents();

        $encoded = (array)json_decode($response);

        if ($encoded['id'] && $encoded['email']) {
            return response(json_encode(true));
        }
        return response(json_encode(false));
    }
}
