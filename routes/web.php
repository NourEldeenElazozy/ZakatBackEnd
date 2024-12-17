<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZakatNisabController;
use App\Http\Controllers\ImageController;
Route::get('/images', [ImageController::class, 'index'])->name('images.index');

// حذف صورة
Route::delete('/images/{id}', [ImageController::class, 'delete'])->name('image.delete');

// رفع صورة
Route::post('/upload-image', [ImageController::class, 'uploadImage'])->name('image.upload');
Route::get('/run-migrations', function () {
    try {
        // استدعاء الترحيل باستخدام أمر Artisan
        Artisan::call('migrate');
        return "تم تشغيل الترحيل بنجاح";
    } catch (Exception $e) {
        // التعامل مع أي خطأ محتمل
        return "حدث خطأ أثناء تشغيل الترحيل: " . $e->getMessage();
    }
});
Route::get('/', function () {
    return view('auth.login');


});

Auth::routes(['register' => false]);

Route::resource('zakat_nisab', ZakatNisabController::class);
Route::resource( 'categories' ,CategoriesController::class);

Route::resource( 'campaign' ,CampaignController::class);

Route::resource( 'donation' ,DonationsController::class);

Route::get('donations/{id}',[ CampaignController::class ,'index2']);


Route::middleware('auth')->group(function () {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);

});




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/{page}', 'App\Http\Controllers\AdminController@index');


