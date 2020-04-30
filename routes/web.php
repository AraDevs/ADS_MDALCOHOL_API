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

$router->post('/login', 'UserController@login');

//Routes that both user types can access
$router->group(['middleware' => 'auth'], function () use ($router) {

    $router->get('/logout', 'UserController@logout');

    $router->get('/departments', 'DepartmentController@index');

    $router->get('/municipalities', 'MunicipalityController@index');
    $router->get('/municipalities/{id}', 'MunicipalityController@show');
    $router->get('/municipalities/departments/{departmentId}', 'MunicipalityController@getByDepartmentId');
    
    $router->get('/providers', 'ProviderController@index');
    $router->get('/providers/active', 'ProviderController@getActiveProviders');
    $router->get('/providers/{id}', 'ProviderController@show');

    $router->get('/production_orders', 'ProductionOrderController@index');
    $router->get('/production_orders/active', 'ProductionOrderController@getActiveOrders');
    $router->get('/production_orders/finished', 'ProductionOrderController@getFinishedOrders');
    $router->get('/production_orders/unfinished', 'ProductionOrderController@getUnfinishedOrders');
    $router->get('/production_orders/{id}', 'ProductionOrderController@show');

    $router->get('/inventories', 'InventoryController@index');
    $router->get('/inventories/active', 'InventoryController@getActiveInventories');
    $router->get('/inventories/final_products', 'InventoryController@getActiveFinalProducts');
    $router->get('/inventories/raw_materials', 'InventoryController@getActiveRawMaterials');
    $router->get('/inventories/{id}', 'InventoryController@show');
    $router->get('/inventories/client/{clientId}', 'InventoryController@getProductsByClient');
});

//Routes that are exclusive to Administration Managers
$router->group(['middleware' => 'admin'], function () use ($router) {

    $router->get('/clients', 'ClientController@index');
    $router->get('/clients/active', 'ClientController@getActiveClients');
    $router->get('/clients/{id}', 'ClientController@show');
    $router->post('/clients', 'ClientController@store');
    $router->put('/clients', 'ClientController@update');

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

    $router->get('/special_prices', 'SpecialPriceController@index');
    $router->get('/special_prices/active', 'SpecialPriceController@getActivePrices');
    $router->get('/special_prices/inventories/{inventoryId}', 'SpecialPriceController@getPricesByInventory');
    $router->get('/special_prices/clients/{clientId}', 'SpecialPriceController@getPricesByClient');
    $router->get('/special_prices/clients/{clientId}/inventories/{inventoryId}', 'SpecialPriceController@getPriceByInventoryAndClient');
    $router->get('/special_prices/{id}', 'SpecialPriceController@show');
    $router->post('/special_prices', 'SpecialPriceController@store');

    $router->get('/bills', 'BillController@index');
    $router->get('/bills/active', 'BillController@getActiveBills');
    $router->get('/bills/deleted', 'BillController@getDeletedBills');
    $router->get('/bills/{id}', 'BillController@show');
    $router->post('/bills', 'BillController@store');
    $router->put('/bills', 'BillController@update');

    $router->get('/purchases', 'PurchaseController@index');
    $router->get('/purchases/active', 'PurchaseController@getActivePurchases');
    $router->get('/purchases/deleted', 'PurchaseController@getDeletedPurchases');
    $router->get('/purchases/{id}', 'PurchaseController@show');
    $router->post('/purchases', 'PurchaseController@store');
    $router->put('/purchases', 'PurchaseController@update');
});

//Routes that are exclusive to Production Managers
$router->group(['middleware' => 'prod'], function () use ($router) {

    $router->post('/production_orders', 'ProductionOrderController@store');
    $router->put('/production_orders', 'ProductionOrderController@update');
    $router->put('/production_orders/finish', 'ProductionOrderController@finishOrder');

    $router->post('/inventories', 'InventoryController@store');
    $router->put('/inventories', 'InventoryController@update');

});

Route::get('/reports/sales/by_client/{id}','PdfController@salesByClient');