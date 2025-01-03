<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::prefix('v1')->group(function () {

    Route::get('get-data-sitemap', [ClientController::class, 'getDataSitemap']);
    Route::get('get-data-web', [ClientController::class, 'getDataWeb']);
    Route::get('get-data-home', [ClientController::class, 'getDataHome']);
    Route::get('get-data-products', [ClientController::class, 'getProducts']);
        Route::get('get-data-products-in-filter', [ClientController::class, 'getProductsInFilter']);

    Route::get('get-detail-product/{slug}', [ClientController::class, 'getDetailProduct']);
    Route::get('get-wishlist-count-with-product/{slug}', [ClientController::class, 'getWishlistCountWithProduct']);
    Route::post('product/rating', [ClientController::class, 'ratingProduct']);
    Route::get('get-episode/{slug}/{episode_slug}', [ClientController::class, 'getDataEpisode']);
    Route::get('/get-data-page-category/{slug}', [ClientController::class, 'getDataPageCategory']);
    Route::get('/increment-views/{slug}', [ClientController::class, 'incrementViews']);
    Route::get('get-data-trending-products', [ClientController::class, 'getTrendingProducts']);

    Route::get('/get-data-products-by-type/{slug}', [ClientController::class, 'getProductsByType']);
    Route::get('/get-data-products-by-nation/{slug}', [ClientController::class, 'getProductsByNation']);

    Route::get('/get-data-page-year/{slug}', [ClientController::class, 'getDataPageYear']);
    Route::get('/get-search-suggest/{searchText}', [ClientController::class, 'getSearchSuggest']);

    Route::post('/register', [CustomerController::class, 'register']);
    Route::post('/login', [CustomerController::class, 'login']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/check-product-is-wishlist/{slug}', [CustomerController::class, 'checkProductIsWishlist']);


        Route::get('/customer', [CustomerController::class, 'loadCustomer']);
        Route::get('/count-new-notification', [CustomerController::class, 'loadCountNewNotification']);


        Route::get('/notifications', [CustomerController::class, 'loadNotfications']);
        Route::get('/set-is-view-notfications', [CustomerController::class, 'setIsViewNotification']);

        Route::get('/wishlists', [CustomerController::class, 'loadWishlist']);
        Route::post('/toggle-wishlist', [CustomerController::class, 'toogleWishlist']);


    });

});
