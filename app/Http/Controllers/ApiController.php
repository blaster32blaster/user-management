<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{

    public function accessToken(LoginRequest $request)
    {
        $user = User::where("email", $request->email)->first();

        if ($user) {

            if (Hash::check($request->password, $user->password)) {
//                $refreshToken = $user->createToken('Todo App')->refreshToken;
                return $this->prepareResult(true, ["accessToken" => $user->createToken('Test Application')->accessToken], [], "User Verified");

            } else {

                return $this->prepareResult(false, [], ["password" => "Wrong passowrd"], "Password not matched");

            }

        } else {

            return $this->prepareResult(false, [], ["email" => "Unable to find user"], "User not found");
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param $status
     * @param $data
     * @param $errors
     * @param $msg
     *
     * @return array
     */

    private function prepareResult($status, $data, $errors, $msg)

    {

        return ['status' => $status, 'data' => $data, 'message' => $msg, 'errors' => $errors];

    }
}
