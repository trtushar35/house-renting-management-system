<?php

use App\Http\Controllers\Frontend\SavedPropertyController;
use App\Http\Controllers\Frontend\ApplicantController;
use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\Frontend\FlatController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\HouseController;
use App\Http\Controllers\Frontend\HouseOwnerController;
use App\Http\Controllers\Frontend\LoginController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\Frontend\ProfileController;
use Illuminate\Support\Facades\Artisan;
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


Route::get('/cache-clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('clear-compiled');
    Artisan::call('optimize:clear');
    Artisan::call('storage:link');
    // Artisan::call('optimize');
    session()->flash('message', 'System Updated Successfully.');
    return redirect()->route('frontend.home');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/all/property', [HomeController::class, 'allProperty'])->name('all.property');
Route::get('/dhaka/division/property', [HomeController::class, 'dhakaDivision'])->name('dhaka.division');
Route::get('/khulna/division/property', [HomeController::class, 'khulnaDivision'])->name('khulna.division');
Route::get('/mymensingh/division/property', [HomeController::class, 'mymensinghDivision'])->name('mymensingh.division');
Route::get('/search-flat', [HomeController::class,'searchFlat'])->name('search.flat');

Route::get('/registration', [LoginController::class, 'registration'])->name('registration');
Route::post('/registration-post', [LoginController::class, 'registrationPost'])->name('registration.post');

Route::get('/user/login', [LoginController::class, 'loginPage'])->name('user.login');
Route::post('/login-post', [LoginController::class, 'logInPost'])->name('login.post');

Route::group(['prefix' => 'frontend',], function () {
    Route::match(['get', 'post'], '/logout', [LoginController::class, 'logOut'])->name('logout');

    Route::get('/view/profile', [ProfileController::class,'profileView'])->name('profile.view');
    Route::get('/edit/profile/{id}', [ProfileController::class,'profileEdit'])->name('profile.edit');
    Route::put('/update/profile/{id}', [ProfileController::class,'profileUpdate'])->name('profile.update');

    Route::get('/tenant/list/{id}', [HouseOwnerController::class,'tenantList'])->name('tenant.list');
    Route::get('/tenant/details/{id}', [HouseOwnerController::class,'tenantDetails'])->name('tenant.details');

    Route::get('/payment/form/{id}',[PaymentController::class, 'paymentForm'])->name('payment.form');
    Route::get('/payment/{id}', [PaymentController::class, 'initiatePayment'])->name('initiate.payment');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/manual/payment/', [PaymentController::class, 'manualPayment'])->name('manual.payment');
    Route::post('/manual/payment/{id}', [PaymentController::class, 'manualPaymentStore'])->name('manual.payment.store');

    Route::get('/house/list', [HouseController::class, 'houseList'])->name('house.list');
    Route::get('/create/house', [HouseController::class, 'houseCreate'])->name('house.create');
    Route::post('/store/house', [HouseController::class, 'houseStore'])->name('house.store');
    Route::get('/edit/house/{id}', [HouseController::class, 'houseEdit'])->name('house.edit');
    Route::put('/update/house/{id}', [HouseController::class, 'houseUpdate'])->name('house.update');
    Route::get('/house/details/{flatId}', [HouseController::class, 'houseDetails'])->name('single.house.details');
    Route::get('/house/delete/{flatId}', [HouseController::class, 'houseDelete'])->name('house.delete');

    Route::get('/create/flat/{houseId}', [FlatController::class, 'flatCreate'])->name('flat.create');
    Route::post('/store/flat', [FlatController::class, 'flatStore'])->name('flat.store');
    Route::get('/edit/flat/{id}', [FlatController::class, 'flatEdit'])->name('flat.edit');
    Route::put('/update/flat/{id}', [FlatController::class, 'flatUpdate'])->name('flat.update');
    Route::get('/flat/list/{houseId}', [FlatController::class, 'flatList'])->name('flat.list');
    Route::get('/single/flat/details/{flatId}', [FlatController::class, 'singleFlat'])->name('single.flat.details');
    Route::put('/single/flat/image/upload/{flatId}', [FlatController::class, 'singleFlatImageUpload'])->name('single.flat.image');
    Route::get('/single/flat/image/delete/{flatId}', [FlatController::class, 'singleFlatImageDelete'])->name('single.flat.image.delete');
    Route::get('/flat/delete/{flatId}', [FlatController::class, 'flatDelete'])->name('flat.delete');

    Route::get('/booking-list/{userId}', [BookingController::class, 'bookingList'])->name('booking.list');
    Route::get('/cancel/booking/{id}', [BookingController::class, 'cancelBooking'])->name('cancel.booking');
    Route::get('/book-now/{flatId}', [BookingController::class, 'bookNow'])->name('book.now');

    Route::get('/applicant/list/{id}', [ApplicantController::class, 'applicantList'])->name('applicant.list');
    Route::get('/applicant/accept/{id}', [ApplicantController::class, 'applicantAccept'])->name('applicant.accept');
    Route::get('/applicant/reject/{id}', [ApplicantController::class, 'applicantReject'])->name('applicant.reject');

    Route::get('/saved/list/{id}', [SavedPropertyController::class, 'savedList'])->name('saved.list');
    Route::get('/add/saved/{id}', [SavedPropertyController::class, 'addToSave'])->name('add.to.save');
    Route::get('/saved/property/view/{id}', [SavedPropertyController::class, 'savedPropertyView'])->name('saved.property.view');
    Route::get('/saved/property/delete/{id}', [SavedPropertyController::class, 'savedPropertyDelete'])->name('saved.property.delete');

    Route::group(['middleware' => ['tenantCheck', 'ownerCheck']], function () {
    });
});
