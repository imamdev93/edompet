<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayableController;
use App\Http\Controllers\ReceivableController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
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

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::prefix('admin')->group(function () {
    Route::get('login', [AuthController::class, 'showFormLogin'])->name('admin.login');
    Route::post('login', [AuthController::class, 'doLogin'])->name('admin.doLogin');

    Route::middleware('auth')->group(function () {
        Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
        Route::get('dashboard', DashboardController::class)->name('admin.dashboard');
        Route::resource('user', UserController::class);
        Route::resource('category', CategoryController::class);
        Route::resource('wallet', WalletController::class);
        Route::resource('transaction', TransactionController::class);
        Route::resource('transfer', TransferController::class);
        Route::resource('payable', PayableController::class);
        Route::post('payable/payment', [PayableController::class, 'payment'])->name('payable.payment');
        Route::resource('receivable', ReceivableController::class);
        Route::post('receivable/payment', [ReceivableController::class, 'payment'])->name('receivable.payment');
    });
});
