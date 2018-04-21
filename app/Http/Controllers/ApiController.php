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
use Laravel\Passport\Http\Controllers\AccessTokenController;
use Psr\Http\Message\ServerRequestInterface;

class ApiController extends Controller
{

//    public function accessToken(LoginRequest $request)
//    {
//        $user = User::where("email", $request->email)->first();
//
//        if ($user) {
//
//            if (Hash::check($request->password, $user->password)) {
////                $refreshToken = $user->createToken('Todo App')->refreshToken;
//                return $this->prepareResult(true, ["accessToken" => $user->createToken('Test Application')->accessToken], [], "User Verified");
//
//            } else {
//
//                return $this->prepareResult(false, [], ["password" => "Wrong passowrd"], "Password not matched");
//
//            }
//
//        } else {
//
//            return $this->prepareResult(false, [], ["email" => "Unable to find user"], "User not found");
//        }
//    }

//    private function prepareResult($status, $data, $errors, $msg)
//
//    {
//
//        return ['status' => $status, 'data' => $data, 'message' => $msg, 'errors' => $errors];
//
//    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed
     */
    public function passwordGrantProxy(ServerRequestInterface $request)
    {
        $referer = $request->getServerParams()['HTTP_REFERER'];

        $args = [
                'username' => $request->getParsedBody()['username'],
                'password' => $request->getParsedBody()['password']
            ];

            $client = config('acceptedoauthclients.'. $referer)
                ? config('acceptedoauthclients.'. $referer)
                : '';
        $tokens =  app(AccessTokenController::class)
            ->issueToken($request->withParsedBody(array_merge(
                $args,
                $client)));
        $parsed = json_decode($tokens->getContent());
        return response(json_encode(['token' => $parsed->access_token]));

    }
}
