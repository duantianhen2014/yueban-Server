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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


$api = app('Dingo\Api\Routing\Router');


//app('Dingo\Api\Auth\Auth')->extend('jwt', function ($app) {
//    return new Dingo\Api\Auth\Provider\JWT($app['Tymon\JWTAuth\JWTAuth']);
//});


$api->version('v1', function ($api) {


    $api->group(['namespace' => 'App\Api'], function ($api) {

        $api->get('users/me', 'UsersController@me');

        $api->get('users/{user}', 'UsersController@show');


        $api->post('register','AuthController@register');
        $api->post('login','AuthController@loginUser');
        $api->post('seedCode','AuthController@sendCheckCode');
    });



});
