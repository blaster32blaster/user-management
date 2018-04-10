<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login','ApiController@accessToken');

Route::get('/redirect', function () {
    $query = http_build_query([
        'client_id' => '6',
        'redirect_uri' => 'http://localhost:8080',
        'response_type' => 'code',
        'scope' => '',
    ]);

    return redirect('http://user.management.local/oauth/authorize?'.$query);
});

Route::group(['middleware' => ['web','auth:api']], function()

{


});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
