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

// TODO 管理者ホーム画面暫定
Route::get('manae/home', function () {
    return view('manage/home');
})->name('manageHome');



// 管理ログイン
route::get('manage/login', [LoginController::class, 'index'])->name('manageLogin');
route::post('manage/login', [LoginController::class, 'auth'])->name('manageLoginAuth');
route::any('manage/logout', [LoginController::class, 'logout'])->name('manageLogout');





