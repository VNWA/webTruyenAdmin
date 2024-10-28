<?php

use App\Http\Controllers\TestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\VinawebappController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\FileController;

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\NationController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\EpisodeController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\ProductBannerController;
use App\Http\Controllers\ProductController;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::prefix('vnwa-asdghuajsdg-import-crawl')->group(function () {
    Route::prefix('manga18fx')->group(function () {
        Route::post('/import-crawl-18', [ProductController::class, 'importManga18fxCrawl']);
    });
    Route::post('/import-crawl-18-products', [ProductController::class, 'import18ProductCrawl']);



});
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::post('/ckediter-uploads-file', [FileController::class, 'ckediterUploadsImage']);
    Route::post('/change-status', [VinawebappController::class, 'changeStatus']);
    Route::post('/change-highlight', [VinawebappController::class, 'changeHighlight']);
    Route::post('/delete-items', [VinawebappController::class, 'deleteItems']);
    Route::post('/restore-items', [VinawebappController::class, 'restoreItems']);
    Route::post('/change-ord', [VinawebappController::class, 'changeORD']);
    Route::post('/get-data-district/{id}', [VinawebappController::class, 'getDataDistrict']);
    Route::post('/get-data-ward/{id}', [VinawebappController::class, 'getDataWard']);
    Route::get('/dashboard', [VinawebappController::class, 'showDashboard'])->name('dashboard');

    // start Company
    Route::prefix('company')->group(function () {
        Route::get('', [CompanyController::class, 'showCompany'])->name('Company');
        Route::post('', [CompanyController::class, 'UpdateCompany']);
    });
    // end Company

    // start  category
    Route::prefix('category')->group(function () {
        Route::get('', [CategoryController::class, 'showIndex'])->name('Category');
        Route::post('load-data-table', [CategoryController::class, 'loadDataTable']);
        Route::get('/edit/{id}', [CategoryController::class, 'showEdit'])->name('Category.Edit');
        Route::post('/edit/{id}', [CategoryController::class, 'update']);
    });
    // end category

    // start  Year
    Route::prefix('year')->group(function () {
        Route::get('', [YearController::class, 'showIndex'])->name('Year');
        Route::post('load-data-table', [YearController::class, 'loadDataTable']);
        Route::get('/create', function () {
            return Inertia::render('Year/Create');
        })->name('Year.Create');
        Route::post('/create', [YearController::class, 'create']);

        Route::get('/edit/{id}', [YearController::class, 'showEdit'])->name('Year.Edit');
        Route::post('/edit/{id}', [YearController::class, 'update']);
    });
    // end  Year

    // start  Nation
    Route::prefix('nation')->group(function () {
        Route::get('', [NationController::class, 'showIndex'])->name('Nation');
        Route::post('load-data-table', [NationController::class, 'loadDataTable']);
        Route::get('/create', function () {
            return Inertia::render('Nation/Create');
        })->name('Nation.Create');
        Route::post('/create', [NationController::class, 'create']);

        Route::get('/edit/{id}', [NationController::class, 'showEdit'])->name('Nation.Edit');
        Route::post('/edit/{id}', [NationController::class, 'update']);
    });
    // end  Nation

    // start  Type
    Route::prefix('type')->group(function () {
        Route::get('', [TypeController::class, 'showIndex'])->name('Type');
        Route::post('load-data-table', [TypeController::class, 'loadDataTable']);
        Route::get('/create', function () {
            return Inertia::render('Type/Create');
        })->name('Type.Create');
        Route::post('/create', [TypeController::class, 'create']);

        Route::get('/edit/{id}', [TypeController::class, 'showEdit'])->name('Type.Edit');
        Route::post('/edit/{id}', [TypeController::class, 'update']);
    });
    // end  Type

    // start  truyện Bộ
    Route::prefix('products')->group(function () {
        Route::get('', [ProductController::class, 'showIndex'])->name('Product');
        Route::get('load-data-table', [ProductController::class, 'loadDataTable']);
        Route::get('/trash', [ProductController::class, 'showTrash'])->name('Product.Trash');
        Route::get('/create', [ProductController::class, 'showCreate'])->name('Product.Create');
        Route::post('/create', [ProductController::class, 'create']);
        Route::get('/edit/{id}', [ProductController::class, 'showEdit'])->name('Product.edit');
        Route::post('/edit/{id}', [ProductController::class, 'update']);

        Route::prefix('episode/{id_product}')->group(function () {

            Route::get('/', [EpisodeController::class, 'index'])->name('Product.Episode');
            Route::get('/load-data-table', [EpisodeController::class, 'loadDataTable']);
            Route::post('/create-mutiple', [EpisodeController::class, 'importMultipleZip'])->name('Episode.Import');
            Route::post('/delete', [EpisodeController::class, 'delete'])->name('Episode.Delete');

            Route::post('/create', [EpisodeController::class, 'create']);
            Route::post('/update/{id}', [EpisodeController::class, 'update'])->name('Episode.Update');
            Route::prefix('server/{id_episode}')->group(function () {
                Route::post('/create', [ServerController::class, 'create']);
                Route::post('/update/{id}', [ServerController::class, 'update']);
            });
        });
        // end Tập truyện
        // start  Server truyện


    });
    // end  truyện Bộ



    // start  Tập truyện

    // end Server truyện

    // start  Banner
    Route::prefix('product-banner')->group(function () {
        Route::get('', [ProductBannerController::class, 'showIndex'])->name('ProductBanner');
        Route::post('load-data-table', [ProductBannerController::class, 'loadDataTable']);
        Route::post('load-data-product', [ProductBannerController::class, 'getDataProduct']);
        Route::post('add-product-in-product-banner', [ProductBannerController::class, 'addProductInProductBanner']);
    });
    // end Banner
});
