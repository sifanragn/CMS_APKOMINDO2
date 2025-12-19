<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\Api\ApiAgendaController;
use App\Http\Controllers\Api\ApiAnggotaController;
use App\Http\Controllers\Api\ApiApplicationController;
use App\Http\Controllers\Api\ApiCareerController;
use App\Http\Controllers\Api\ApiHowsController;
use App\Http\Controllers\Api\ApiKegiatanController;
use App\Http\Controllers\Api\ApiOurblogController;
use App\Http\Controllers\Api\ApiProductsController;
use App\Http\Controllers\Api\ApiSliderController;
use App\Http\Controllers\Api\ApiTentangkamiController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HowsController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\OurblogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TentangkamiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Api\ApiArticleController;


// Info user login (dengan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ==============================
//  Public API Routes (GET only)
// ==============================
Route::get('/hows', [ApiHowsController::class, 'index']);
Route::get('/hows/{id}', [ApiHowsController::class, 'show']);

Route::get('/agendas', [ApiAgendaController::class, 'index']);
Route::get('/agendas/{id}', [ApiAgendaController::class, 'show']);

Route::get('/tentangkami', [ApiTentangkamiController::class, 'index']);
Route::get('/category/{id}', [ApiTentangkamiController::class, 'getByCategory']);
Route::get('/category-name/{name}', [ApiTentangkamiController::class, 'getByCategoryName']);
Route::get('/display/home', [ApiTentangkamiController::class, 'getDisplayOnHome']);

Route::get('/career', [ApiCareerController::class, 'index']);
Route::get('/career/{id}', [ApiCareerController::class, 'show']);

Route::get('/ourblog', [ApiOurblogController::class, 'index']);
Route::get('/ourblog/{id}', [ApiOurblogController::class, 'show']);

Route::get('/products', [ApiProductsController::class, 'index']);
Route::get('/products/{id}', [ApiProductsController::class, 'show']);
Route::get('/products/category/{categoryId}', [ApiProductsController::class, 'getByCategory']);

Route::get('/slider', [ApiSliderController::class, 'index']);
Route::get('/slider/home', [ApiSliderController::class, 'showHomeSlider']);

Route::get('/kegiatan', [ApiKegiatanController::class, 'index']);
Route::get('/kegiatan/{id}', [ApiKegiatanController::class, 'show']);
Route::get('/kegiatan/category/{id}', [ApiKegiatanController::class, 'byCategory']);

Route::get('/articles', [ApiArticleController::class, 'index']);
Route::get('/articles/{slug}', [ApiArticleController::class, 'show']);

Route::get('/anggota', [ApiAnggotaController::class, 'index']);

Route::get('/applications', [ApiApplicationController::class, 'index']);
Route::get('/applications/{id}', [ApiApplicationController::class, 'show']);
Route::post('/applications', [ApiApplicationController::class, 'store']);

// ==============================
//  Protected API Routes (auth:sanctum)
// ==============================
Route::middleware(['auth:sanctum'])->group(function () {

    Route::prefix('articles')->name('api.articles.')->group(function () {
        Route::post('/', [ApiArticleController::class, 'store'])->name('store');
        Route::put('/{id}', [ApiArticleController::class, 'update'])->name('update');
        Route::delete('/{id}', [ApiArticleController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('hows')->name('api.hows.')->group(function () {
        Route::post('/', [HowsController::class, 'store'])->name('store');
        Route::put('/{id}', [HowsController::class, 'update'])->name('update');
        Route::delete('/{id}', [HowsController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('products')->name('api.products.')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::prefix('tentangkami')->name('api.tentangkami.')->group(function () {
        Route::post('/', [TentangkamiController::class, 'store'])->name('store');
        Route::put('/{id}', [TentangkamiController::class, 'update'])->name('update');
        Route::delete('/{id}', [TentangkamiController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('ourblogs')->name('api.ourblogs.')->group(function () {
        Route::post('/', [OurblogController::class, 'store'])->name('store');
        Route::put('/{id}', [OurblogController::class, 'update'])->name('update');
        Route::delete('/{id}', [OurblogController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [OurblogController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::prefix('category')->name('api.category.')->group(function () {
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('agenda')->name('api.agenda.')->group(function () {
        Route::post('/', [AgendaController::class, 'store'])->name('store');
        Route::put('/{id}', [AgendaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AgendaController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [AgendaController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::prefix('career')->name('api.career.')->group(function () {
        Route::post('/', [CareerController::class, 'store'])->name('store');
        Route::put('/{career}', [CareerController::class, 'update'])->name('update');
        Route::delete('/{id}', [CareerController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('slider')->name('api.slider.')->group(function () {
        Route::post('/', [SliderController::class, 'store'])->name('store');
        Route::put('/{id}', [SliderController::class, 'update'])->name('update');
        Route::delete('/{id}', [SliderController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('anggota')->name('api.anggota.')->group(function () {
        Route::post('/', [AnggotaController::class, 'store'])->name('store');
        Route::put('/{id}', [AnggotaController::class, 'update'])->name('update');
        Route::delete('/{id}', [AnggotaController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [AnggotaController::class, 'bulkDelete'])->name('bulkDelete');
    });

    Route::prefix('kegiatan')->name('api.kegiatan.')->group(function () {
        Route::post('/', [KegiatanController::class, 'store'])->name('store');
        Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
        Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [KegiatanController::class, 'bulkDelete'])->name('bulkDelete');
    });

    // Logout
    Route::post('/logout', [UserController::class, 'logout']);
});

// Login route
Route::post('/login', [UserController::class, 'login']);
