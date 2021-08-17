<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Manage\LoginController;

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

Route::get('manae/welcome', function () {
    return view('welcome');
})->name('home');
route::get('manage/disp', [LoginController::class, 'disp'])->name('manageDisp')->middleware('authManage');
route::get('admin', [LoginController::class, 'index']);


// 管理ログイン
route::get('manage/login', [LoginController::class, 'index'])->name('manageLogin');
route::post('manage/login', [LoginController::class, 'auth'])->name('manageLoginAuth');
route::any('manage/logout', [LoginController::class, 'logout'])->name('manageLogout');





