<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\AdminUserController;
use App\Http\Controllers\Manage\AdminHomeController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\ConvertFontController;
use App\Http\Controllers\Manage\DataEditController;
use App\Http\Controllers\User\LoginController as UserLogin;
use App\Http\Controllers\User\HomeController;

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

// 管理ログイン
route::get('manage/login', [LoginController::class, 'index'])->name('manageLogin');
route::post('manage/login', [LoginController::class, 'login'])->name('manageLoginAuth');
route::any('manage/logout', [LoginController::class, 'logout'])->name('manageLogout');

// 管理ホーム
route::get('manage/home', [AdminHomeController::class, 'index'])->name('manageHome')->middleware('authManage');

// 管理ユーザー一覧
route::get('manage/adminUser', [AdminUserController::class, 'index'])->name('manageAdminUser')->middleware('authManage');
route::post('manage/adminUser/search', [AdminUserController::class, 'search'])->name('manageAdminUserSearch')->middleware('authManage');
route::post('manage/adminUser/update', [AdminUserController::class, 'update'])->name('manageAdminUserUpdate')->middleware('authManage');

// ユーザー一覧
route::get('manage/user', [UserController::class, 'index'])->name('manageUser')->middleware('authManage');
route::post('manage/user/search', [UserController::class, 'search'])->name('manageUserSearch')->middleware('authManage');
route::get('manage/user/detail/{editId?}', [UserController::class, 'detail'])->name('manageUserDetail')->middleware('authManage');

// 旧字体変換マスタ
route::get('manage/convertFont', [ConvertFontController::class, 'index'])->name('manageConvertFont')->middleware('authManage');
route::get('manage/ConvertFont/edit/{editId?}', [ConvertFontController::class, 'edit'])->name('manageConvertFontEdit')->middleware('authManage');
route::post('manage/ConvertFont/delete/{editId?}', [ConvertFontController::class, 'delete'])->name('manageConvertFontDelete')->middleware('authManage');
route::post('manage/ConvertFont/update', [ConvertFontController::class, 'update'])->name('manageConvertFontUpdate')->middleware('authManage');

// データ登録変更画面
route::get('manage/dataEdit', [DataEditController::class, 'index'])->name('manageDataEdit')->middleware('authManage');
route::post('manage/dataEdit/search', [DataEditController::class, 'search'])->name('manageDataEditSearch')->middleware('authManage');
route::get('manage/dataEdit/edit/corporation/{editId?}', [DataEditController::class, 'editCorporation'])->name('manageDataEditEditCorporation')->middleware('authManage');
route::get('manage/dataEdit/edit/person/{editId?}', [DataEditController::class, 'editPerson'])->name('manageDataEditEditPerson')->middleware('authManage');
route::post('manage/dataEdit/update/corporation', [DataEditController::class, 'updateCorporation'])->name('manageDataEditUpdateCorporation')->middleware('authManage');
route::post('manage/dataEdit/update/person', [DataEditController::class, 'updatePerson'])->name('manageDataEditUpdatePerson')->middleware('authManage');
route::post('manage/dataEdit/delete/corporation/{editId?}', [DataEditController::class, 'deleteCorporation'])->name('manageDataEditDeleteCorporation')->middleware('authManage');
route::post('manage/dataEdit/delete/person/{editId?}', [DataEditController::class, 'deletePerson'])->name('manageDataEditDeletePerson')->middleware('authManage');

//　ユーザーログイン画面
route::get('login', [UserLogin::class, 'index'])->name('userLogin');
route::post('login', [UserLogin::class, 'login'])->name('userLoginAuth');
route::any('logout', [UserLogin::class, 'logout'])->name('userLogout');

// ユーザーホーム画面
route::get('/', [HomeController::class, 'index'])->name('userHome')->middleware('auth');




