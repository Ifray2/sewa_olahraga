<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardcontroller;
use App\Http\Controllers\Admin\AdminEquipmentcontroller;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\AdminRentalController;
use App\Http\Controllers\Admin\AdminReviewcontroller;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUsercontroller;
use App\Http\Controllers\Petugas\PetugasCategoryController;
use App\Http\Controllers\Petugas\PetugasDashboardController;
use App\Http\Controllers\Petugas\PetugasEquipmentcontroller;
use App\Http\Controllers\Petugas\PetugasPaymentController;
use App\Http\Controllers\Petugas\PetugasRentalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RentalController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});


Route::get('/',         [HomeController::class, 'index'])->name('home');
Route::get('/katalog',  [HomeController::class, 'catalog'])->name('catalog');
Route::get('/alat/{equipment}', [HomeController::class, 'equipmentDetail'])->name('equipment.show');


Route::resource('rentals', RentalController::class)->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get   ('transaksi',                  [RentalController::class, 'index'])  ->name('rentals.index');
    Route::get   ('transaksi/baru',             [RentalController::class, 'create']) ->name('rentals.create');
    Route::post  ('transaksi',                  [RentalController::class, 'store'])  ->name('rentals.store');
    Route::get   ('transaksi/{rental}',         [RentalController::class, 'show'])   ->name('rentals.show');
    Route::post  ('transaksi/{rental}/bayar',   [RentalController::class, 'pay'])    ->name('rentals.pay');
    Route::post  ('transaksi/{rental}/ulasan',  [RentalController::class, 'review']) ->name('rentals.review');
    Route::post  ('transaksi/{rental}/batalkan',[RentalController::class, 'cancel']) ->name('rentals.cancel');
});


Route::get('/dashboard', [AdminDashboardcontroller::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



Route::prefix('admin')->name('admin.')->group(function () {

    Route::resource('categories', AdminCategoryController::class);

    // toggle status kategori
    Route::patch(
        'categories/{category}/toggle-status',
        [AdminCategoryController::class, 'toggleStatus']
    )->name('categories.toggleStatus');

    Route::resource('equipment', AdminEquipmentController::class);
    Route::patch('equipment/{equipment}/toggle', [AdminEquipmentcontroller::class, 'toggleAvailability'])
        ->name('equipment.toggle');

    Route::resource('rentals', AdminRentalController::class);

    // Quick action routes
    Route::post('rentals/{rental}/confirm', [AdminRentalController::class, 'confirm'])
        ->name('rentals.confirm');
    Route::post('rentals/{rental}/activate', [AdminRentalController::class, 'activate'])
        ->name('rentals.activate');
    Route::post('rentals/{rental}/return', [AdminRentalController::class, 'markReturned'])
        ->name('rentals.return');
    Route::post('rentals/{rental}/cancel', [AdminRentalController::class, 'cancel'])
        ->name('rentals.cancel');


    Route::resource('payments', AdminPaymentController::class);

    Route::post('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])
         ->name('payments.verify');
    Route::post('payments/{payment}/refund', [AdminPaymentController::class, 'refund'])
         ->name('payments.refund');

    Route::resource('reviews', AdminReviewcontroller::class);

    Route::resource('users', AdminUsercontroller::class);
    Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])
         ->name('users.reset-password');

    Route::get ('settings',          [AdminSettingController::class, 'index'])          ->name('settings.index');
    Route::put ('settings/profile',  [AdminSettingController::class, 'updateProfile'])  ->name('settings.profile');
    Route::put ('settings/password', [AdminSettingController::class, 'updatePassword']) ->name('settings.password');

    // Khusus admin — implementasi dengan Artisan::call() di controller
    Route::post('settings/clear-cache',       [AdminSettingController::class, 'clearCache'])      ->name('settings.clear-cache');
    Route::post('settings/clear-config',      [AdminSettingController::class, 'clearConfig'])     ->name('settings.clear-config');
    Route::post('settings/clear-routes',      [AdminSettingController::class, 'clearRoutes'])     ->name('settings.clear-routes');
    Route::post('settings/clear-views',       [AdminSettingController::class, 'clearViews'])      ->name('settings.clear-views');
    Route::post('settings/clear-all',         [AdminSettingController::class, 'clearAll'])        ->name('settings.clear-all');
    Route::post('settings/maintenance-down',  [AdminSettingController::class, 'maintenanceDown']) ->name('settings.maintenance-down');
    Route::post('settings/maintenance-up',    [AdminSettingController::class, 'maintenanceUp'])   ->name('settings.maintenance-up');
    Route::post('settings/logout-all',        [AdminSettingController::class, 'logoutAll'])       ->name('settings.logout-all');

});

Route::prefix('petugas')->name('petugas.')->group(function () {

    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');


    Route::resource('categories', PetugasCategoryController::class);
    Route::patch(
        'categories/{category}/toggle-status',
        [AdminCategoryController::class, 'toggleStatus']
    )->name('categories.toggleStatus');

    Route::resource('equipment', PetugasEquipmentcontroller::class);
    Route::patch('equipment/{equipment}/toggle', [PetugasEquipmentcontroller::class, 'toggleAvailability'])
        ->name('equipment.toggle');

    Route::resource('rentals', PetugasRentalController::class);

    // Quick action routes
    Route::post('rentals/{rental}/confirm', [PetugasRentalController::class, 'confirm'])
        ->name('rentals.confirm');
    Route::post('rentals/{rental}/activate', [PetugasRentalController::class, 'activate'])
        ->name('rentals.activate');
    Route::post('rentals/{rental}/return', [PetugasRentalController::class, 'markReturned'])
        ->name('rentals.return');
    Route::post('rentals/{rental}/cancel', [PetugasRentalController::class, 'cancel'])
        ->name('rentals.cancel');

    
    Route::resource('payments', PetugasPaymentController::class);

    Route::post('payments/{payment}/verify', [PetugasPaymentController::class, 'verify'])
         ->name('payments.verify');
    Route::post('payments/{payment}/refund', [PetugasPaymentController::class, 'refund'])
         ->name('payments.refund');


});

require __DIR__ . '/auth.php';
