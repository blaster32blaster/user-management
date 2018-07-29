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

//Route::post('/login','ApiController@accessToken');

Route::group(['prefix' => 'oauth-proxy'], function () {
        Route::post('password', 'ApiController@passwordGrantProxy');

        Route::get('authorization', 'ApiController@authorizationProxy');

    Route::get('login/{provider}', 'Auth\SocialAccountController@redirectToProviderApi');

    Route::get('login/{provider}/callback', 'Auth\SocialAccountController@handleProviderCallbackApi');

    Route::group(['prefix' => 'client'], function()
    {
        Route::get('users/{id}', 'ClientsController@users');

        Route::put('users/{id}', 'ClientsController@update');

        Route::delete('users/{id}', 'ClientsController@destroy');

        Route::get('roles/{id}', 'ClientsController@rolesList');
    });

});

Route::group(['middleware' => ['web','auth:api']], function()
{

});

Route::get('/here', function (Request $request) {
    return 'here';
});

Route::post('/reinvite', 'InvitationController@reinvite');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
