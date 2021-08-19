<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\AdminUserController;

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
Route::get('manage/home', function () {
    return view('manage/home');
})->name('manageHome');



// 管理ログイン
route::get('manage/login', [LoginController::class, 'index'])->name('manageLogin');
route::post('manage/login', [LoginController::class, 'auth'])->name('manageLoginAuth');
route::any('manage/logout', [LoginController::class, 'logout'])->name('manageLogout');

// 管理ユーザー一覧
route::get('manage/adminUser', [AdminUserController::class, 'index'])->name('manageAdminUser')->middleware('authManage');
route::post('manage/adminUser/search', [AdminUserController::class, 'search'])->name('manageAdminUserSearch')->middleware('authManage');




