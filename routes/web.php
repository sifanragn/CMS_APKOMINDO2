<?php

use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AgendaSpeakerController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\CategoryAnggotaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HowsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OurblogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryKegiatanController;
use App\Http\Controllers\CategoryStoreController;
use App\Http\Controllers\CategoryTentangkami;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\ProfileSettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SosmedController;
use App\Models\CategoryKegiatan;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\TentangkamiController;
use App\Models\CategoryStore;
use App\Models\TentangkamiCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');



// Route untuk akses file storage
Route::get('/storage/{path}', function ($path) {
    $file = Storage::disk('public')->path($path);

    if (!file_exists($file)) {
        abort(404);
    }

    return response()->file($file, [
        'Access-Control-Allow-Origin' => request()->header('origin') ?: '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => '*',
    ]);
})->where('path', '.*');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/slider-preview', [SliderController::class, 'showHomeSlider'])->name('slider.preview');
Route::get('/slider/{slider}', [SliderController::class, 'show'])->name('slider.show');
Route::put('/slider/{slider}', [SliderController::class, 'update']) ->name('slider.update');



Route::get('/profile-setting', [ProfileSettingController::class, 'index'])->name('profile-setting');

// Company Address

Route::post('/profile-setting', [CompanyProfileController::class, 'store'])->name('company-profile.store');
Route::put('/profile-setting/{id}', [CompanyProfileController::class, 'update'])->name('company-profile.update');
Route::delete('/profile-setting/{id}', [CompanyProfileController::class, 'destroy'])->name('company-profile.destroy');

// Contact (Phone & Email Update)
Route::post('/contact/update', [ContactController::class, 'update'])->name('contact.update');
Route::post('/', [ContactController::class, 'store'])->name('contact.store');
Route::delete('/contact/{id}', [ContactController::class, 'destroy'])->name('contact.destroy');

// Social Accounts (Update Only)
Route::put('/social-account/{socialAccount}/update', [SosmedController::class, 'update'])->name('social.update');
// Benar - tanpa parameter untuk store
Route::post('/social-account/store', [SosmedController::class, 'store'])->name('social.store');
Route::delete('/social-account/{id}', [SosmedController::class, 'destroy'])->name('social.destroy');

Route::prefix('hows')->name('hows.')->group(function () {
    Route::get('/', [HowsController::class, 'index'])->name('index');
    Route::post('/', [HowsController::class, 'store'])->name('store');
    Route::get('/{id}', [HowsController::class, 'show'])->name('show');
    Route::put('/{id}', [HowsController::class, 'update'])->name('update');
    Route::delete('/{id}', [HowsController::class, 'destroy'])->name('destroy');
});


Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::put('/{id}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [ProductController::class, 'bulkDelete'])->name('bulkDelete');
});


Route::prefix('tentangkami')->name('tentangkami.')->group(function () {

    Route::get('/', [TentangkamiController::class, 'index'])->name('index');
    Route::post('/', [TentangkamiController::class, 'store'])->name('store');
    Route::put('/{id}', [TentangkamiController::class, 'update'])->name('update');
    Route::delete('/{id}', [TentangkamiController::class, 'destroy'])->name('destroy');

    Route::post('toggle/{id}', [TentangKamiController::class, 'toggle'])->name('tentangkami.toggle');


    // API routes yang ADA di controller
    Route::get('/category/{categoryId}', [TentangkamiController::class, 'getByCategory'])->name('category');
    Route::get('/category-name/{categoryName}', [TentangkamiController::class, 'getByCategoryName'])->name('category.name');
    Route::get('/display-on-home', [TentangkamiController::class, 'getDisplayOnHome'])->name('display.home');

});

Route::prefix('ourblogs')->name('ourblogs.')->group(function () {
    Route::get('/', [OurblogController::class, 'index'])->name('index');
    Route::get('/{id}', [OurblogController::class, 'show'])->name('show');
    Route::post('/', [OurblogController::class, 'store'])->name('store');
    Route::put('/{id}', [OurblogController::class, 'update'])->name('update');
    Route::delete('/{id}', [OurblogController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [OurblogController::class, 'bulkDelete'])->name('bulkDelete');
});

Route::prefix('category')->name('category.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/store', [CategoryController::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
});

Route::prefix('agenda')->name('agenda.')->group(function () {
    Route::get('/', [AgendaController::class, 'index'])->name('index');
    Route::get('/{id}', [AgendaController::class, 'show'])->name('show');
    Route::post('/', [AgendaController::class, 'store'])->name('store');
    Route::put('/{id}', [AgendaController::class, 'update'])->name('update');
    Route::delete('/{id}', [AgendaController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [AgendaController::class, 'bulkDelete'])->name('bulkDelete');
});

Route::prefix('agenda-speakers')->name('agenda-speakers.')->group(function () {
    Route::get('/', [AgendaSpeakerController::class, 'index'])->name('index');
    Route::get('/{id}', [AgendaSpeakerController::class, 'show'])->name('show');
    Route::post('/', [AgendaSpeakerController::class, 'store'])->name('store');
    Route::put('/{id}', [AgendaSpeakerController::class, 'update'])->name('update');
    Route::delete('/{id}', [AgendaSpeakerController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [AgendaSpeakerController::class, 'bulkDelete'])->name('bulkDelete');
});

Route::prefix('career')->name('career.')->group(function () {
    Route::get('/', [CareerController::class, 'index'])->name('index');
    Route::get('/{id}', [CareerController::class, 'show'])->name('show');
    Route::get('/career/{id}/applicants', [CareerController::class, 'showApplicants'])->name('career.applicants');
    Route::post('/', [CareerController::class, 'store'])->name('store');
    Route::put('/{career}', [CareerController::class, 'update'])->name('update');
    Route::delete('/{id}', [CareerController::class, 'destroy'])->name('destroy');
});

Route::prefix('slider')->name('slider.')->group(function () {
    Route::get('/', [SliderController::class, 'index'])->name('index');
    Route::post('/', [SliderController::class, 'store'])->name('store');
    Route::put('/{id}', [SliderController::class, 'update'])->name('update');
    Route::delete('/{id}', [SliderController::class, 'destroy'])->name('destroy');
    Route::post('toggle/{id}', [SliderController::class, 'toggleDisplay']);

});

Route::prefix('anggota')->name('anggota.')->group(function () {
    Route::get('/', [AnggotaController::class, 'index'])->name('index');
    Route::post('/', [AnggotaController::class, 'store'])->name('store');
    Route::put('/{id}', [AnggotaController::class, 'update'])->name('update');
    Route::delete('/{id}', [AnggotaController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [AnggotaController::class, 'bulkDelete'])->name('bulkDelete');
});

Route::prefix('category-anggota')->name('category-anggota.')->group(function () {
    Route::get('/', [CategoryAnggotaController::class, 'index'])->name('index');
    Route::post('/store', [CategoryAnggotaController::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryAnggotaController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryAnggotaController::class, 'destroy'])->name('destroy');
});

Route::prefix('kegiatan')->name('kegiatan.')->group(function () {
    Route::get('/', [KegiatanController::class, 'index'])->name('index');
    Route::get('/{id}', [KegiatanController::class, 'show'])->name('show');
    Route::post('/', [KegiatanController::class, 'store'])->name('store');
    Route::put('/{id}', [KegiatanController::class, 'update'])->name('update');
    Route::delete('/{id}', [KegiatanController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [KegiatanController::class, 'bulkDelete'])->name('bulkDelete');
    Route::get('/kegiatan/category/{id}', [KegiatanController::class, 'byCategory'])->name('kegiatan.byCategory');
});

Route::prefix('category-kegiatan')->name('category-kegiatan.')->group(function () {
    Route::get('/', [CategoryKegiatanController::class, 'index'])->name('index');
    Route::post('/store', [CategoryKegiatanController::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryKegiatanController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryKegiatanController::class, 'destroy'])->name('destroy');
});

Route::prefix('category-tentangkami')->name('category-tentangkami.')->group(function () {
    Route::get('/', [CategoryTentangkami::class, 'index'])->name('index');
    Route::post('/store', [CategoryTentangkami::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryTentangkami::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryTentangkami::class, 'destroy'])->name('destroy');
});

Route::prefix('category-store')->name('category-store.')->group(function () {
    Route::get('/', [CategoryStoreController::class, 'index'])->name('index');
    Route::post('/store', [CategoryStoreController::class, 'store'])->name('store');
    Route::put('/update/{id}', [CategoryStoreController::class, 'update'])->name('update');
    Route::delete('/{id}', [CategoryStoreController::class, 'destroy'])->name('destroy');
});


Route::resource('applications', ApplicationController::class);

// Untuk route tambahan yang ada di controller:
Route::get('applications/{application}/download', [ApplicationController::class, 'downloadFile'])
    ->name('applications.download');

Route::post('applications/bulk-delete', [ApplicationController::class, 'bulkDelete'])
    ->name('applications.bulkDelete');
