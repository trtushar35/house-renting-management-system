<?php

use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ModuleMakerController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\TenantController;
use App\Http\Controllers\Backend\HouseOwnerController;
use App\Http\Controllers\Backend\HouseController;
use App\Http\Controllers\Backend\FlatController;
use App\Http\Controllers\Backend\RoomController;
use App\Http\Controllers\Backend\BookingController;
use App\Http\Controllers\Backend\PaymentController;
use App\Http\Controllers\Backend\FlatImageController;
use App\Http\Controllers\Backend\SavedPropertyController;
//don't remove this comment from route namespace

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

Route::get('/', [LoginController::class, 'loginPage'])->name('home')->middleware('AuthCheck');

Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    Artisan::call('storage:link');
    Artisan::call('optimize');
    session()->flash('message', 'System Updated Successfully.');

    return redirect()->route('home');
});

Route::group(['as' => 'auth.'], function () {
    Route::get('/login', [LoginController::class, 'loginPage'])->name('login2')->middleware('AuthCheck');
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::group(['middleware' => 'AdminAuth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::match(['get', 'post'], '/module-make', [ModuleMakerController::class, 'index'])->name('moduleMaker');

    Route::resource('admin', AdminController::class);
    Route::get('admin/{id}/status/{status}/change', [AdminController::class, 'changeStatus'])->name('admin.status.change');

    // for role
    Route::resource('role', RoleController::class);

    // for permission entry
    Route::resource('permission', PermissionController::class);


	// for Tenant
	Route::resource('tenant', TenantController::class);
	Route::get('tenant/{id}/status/{status}/change', [TenantController::class, 'changeStatus'])->name('tenant.status.change');

	// for HouseOwner
	Route::resource('houseowner', HouseOwnerController::class);
	Route::get('houseowner/{id}/status/{status}/change', [HouseOwnerController::class, 'changeStatus'])->name('houseowner.status.change');

	// for House
	Route::resource('house', HouseController::class);
    Route::get('house-address/{id}', [HouseController::class, 'houseAddress'])->name('house-price');
	Route::get('house/{id}/status/{status}/change', [HouseController::class, 'changeStatus'])->name('house.status.change');

	// for Flat
	Route::resource('flat', FlatController::class);
    Route::get('booking-price/{id}', [FlatController::class, 'bookingPrice'])->name('booking-price');
	Route::get('flat/{id}/status/{status}/change', [FlatController::class, 'changeStatus'])->name('flat.status.change');

	// for Room
	Route::resource('room', RoomController::class);
    Route::get('booking-price/{id}', [RoomController::class, 'bookingPrice'])->name('booking-price');
	Route::get('room/{id}/status/{status}/change', [RoomController::class, 'changeStatus'])->name('room.status.change');

	// for Booking
	Route::resource('booking', BookingController::class);
	Route::get('booking-price/{id}', [BookingController::class, 'bookingPrice'])->name('booking-price');
	Route::get('booking/{id}/status/{status}/change', [BookingController::class, 'changeStatus'])->name('booking.status.change');

	// for Payment
	Route::resource('payment', PaymentController::class);
	Route::get('payment/{id}/status/{status}/change', [PaymentController::class, 'changeStatus'])->name('payment.status.change');

	// for FlatImage
	Route::resource('flatimage', FlatImageController::class);
	Route::get('flatimage/{id}/status/{status}/change', [FlatImageController::class, 'changeStatus'])->name('flatimage.status.change');

	//don't remove this comment from route body
});
