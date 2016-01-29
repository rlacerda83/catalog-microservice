<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', ['middleware' => 'api.auth', 'namespace' => 'App\Http\Controllers\V1'], function ($api) {

    // Products
    $api->get('products/', 'ProductController@getAllActives');
    $api->get('products/featureds', 'ProductController@getFeaturedProducts');
    $api->get('products/{idSku}/details-page', 'ProductController@getDetailsPage');

    // Categories
    $api->get('categories/', 'CategoryController@index');
    $api->get('categories/tree', 'CategoryController@getTree');
});