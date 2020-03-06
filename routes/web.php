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

$router->get('/departments', 'DepartmentController@index');

$router->get('/municipalities', 'MunicipalityController@index');
$router->get('/municipalities/{id}', 'MunicipalityController@show');
$router->get('/municipalities/departments/{departmentId}', 'MunicipalityController@getByDepartmentId');

$router->get('/clients', 'ClientController@index');
$router->get('/clients/active', 'ClientController@getActiveClients');
$router->get('/clients/{id}', 'ClientController@show');
$router->post('/clients', 'ClientController@store');
$router->put('/clients', 'ClientController@update');

$router->get('/providers', 'ProviderController@index');
$router->get('/providers/active', 'ProviderController@getActiveProviders');
$router->get('/providers/{id}', 'ProviderController@show');
$router->post('/providers', 'ProviderController@store');
$router->put('/providers', 'ProviderController@update');
