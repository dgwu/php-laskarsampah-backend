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


$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->post('/register', 'UserController@register');
$router->post('/login', 'UserController@login');
$router->post('/getuserprofile', 'UserController@getProfile');

$router->post('/getWasteBank','UserController@getWasteBank');
$router->post('/getWasteBankBy','UserController@getWasteBankBy');

$router->post('/getNews','UserController@getNews');
$router->post('/getNewsBy','UserController@getNewsBy');


$router->get('/pricelist', 'WasteController@pricelist');