<?php

use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::controller(RegisterController::class)->group(function () {
    Route::post('register/vendor', 'registerVendor');
    Route::post('login', 'login');
    Route::post('register', 'register');
});


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::get('insetItems' , [HomeController::class , 'insertItems']);
    // customer api
    Route::get('stores' , [HomeController::class , 'getStores']);
    Route::get('items' , [HomeController::class , 'getItems']);
    Route::post('make-order' , [OrderController::class , 'makeOrder']);
    Route::get('customer-orders' , [OrderController::class , 'customerOrders']);

    // delivery api
    Route::get('allOrder' , [OrderController::class , 'allOrders']);
    Route::get('myOrders' , [OrderController::class , 'myOrders']);
    Route::post('get-order' , [OrderController::class , 'getOrder']);
    Route::post('delivery-order' , [OrderController::class , 'orderDelivery']);
    Route::get('wallet' , [HomeController::class , 'wallet']);

});
