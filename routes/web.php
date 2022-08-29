<?php
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', [ProductController::class, 'listProducts']);
Route::group(['prefix' => 'products'], function () {
    Route::get('/prd-id/{q?}', [ProductController::class, 'productDetail']);
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/subscription/create', [SubscriptionController::class, 'index'])->name('subscription.create');
Route::post('/order-post', [SubscriptionController::class, 'orderPost']);

Route::post('/purchase', [SubscriptionController::class, 'purchase']);

//Route::post('/purchase', function (Request $request) {
//    $stripeCharge = $request->user()->charge(
//        2, $request->paymentMethodId
//    );
//});
