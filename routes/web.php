<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['middleware' => 'authc'], function() use ($app) {

	$app->get('/create', function () use($app) {

    // Getting the authenticated user
    $user = Auth::user();

    return "Hello" . $user;
	});

});

$app->post('/createUser','UserController@createUser');


$app->post('/loginUser','AuthController@authenticate');



$app->group(['middleware' => 'authb'], function() use ($app) {

	$app->get('/createC', function () use($app) {

    // Getting the authenticated client
    $client = Auth::user();

    return "Hello" . $client;
	});

});

$app->post('/createClient','ClientController@createClient');

$app->post('/loginClient','AuthControllerC@authenticate');
