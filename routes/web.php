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

$router->get('/sellers', 'SellerController@index');
$router->get('/sellers/active', 'SellerController@getActiveSellers');
$router->get('/sellers/{id}', 'SellerController@show');
$router->post('/sellers', 'SellerController@store');
$router->put('/sellers', 'SellerController@update');

$router->get('/users', 'UserController@index');
$router->get('/users/active', 'UserController@getActiveUsers');
$router->get('/users/{id}', 'UserController@show');
$router->post('/users', 'UserController@store');
$router->put('/users', 'UserController@update');

$router->get('/production_orders', 'ProductionOrderController@index');
$router->get('/production_orders/active', 'ProductionOrderController@getActiveOrders');
$router->get('/production_orders/finished', 'ProductionOrderController@getFinishedOrders');
$router->get('/production_orders/unfinished', 'ProductionOrderController@getUnfinishedOrders');
$router->get('/production_orders/{id}', 'ProductionOrderController@show');
$router->post('/production_orders', 'ProductionOrderController@store');
$router->put('/production_orders', 'ProductionOrderController@update');
$router->put('/production_orders/finish', 'ProductionOrderController@finishOrder');

$router->get('/special_prices', 'SpecialPriceController@index');
$router->get('/special_prices/active', 'SpecialPriceController@getActivePrices');
$router->get('/special_prices/inventories/{inventoryId}', 'SpecialPriceController@getPricesByInventory');
$router->get('/special_prices/clients/{clientId}', 'SpecialPriceController@getPricesByClient');
$router->get('/special_prices/{id}', 'SpecialPriceController@show');
$router->post('/special_prices', 'SpecialPriceController@store');