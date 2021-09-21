<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\AdminUserController;
use App\Http\Controllers\Manage\AdminHomeController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\ConvertFontController;
use App\Http\Controllers\Manage\DataRegisterController;
use App\Http\Controllers\Manage\DataEditController;
use App\Http\Controllers\Manage\ClaimController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\BulkSearchController;
use App\Http\Controllers\User\LoginController as UserLogin;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\User\UseReportController;
use App\Http\Controllers\API\SearchController as SearchAPI;
use App\Http\Controllers\API\UseReportController as UseReportAPI;

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
route::get('manage/user/edit/{editId?}', [UserController::class, 'edit'])->name('manageUserEdit')->middleware('authManage');
route::post('manage/user/edit/update', [UserController::class, 'update'])->name('manageUserUpdate')->middleware('authManage');
route::post('manage/user/detail/changePassword', [UserController::class, 'changePassword'])->name('manageUserChangePassword')->middleware('authManage');
route::post('manage/user/detail/sendUserInfo', [UserController::class, 'sendUserInfo'])->name('manageUserSendUserInfo')->middleware('authManage');

// 旧字体変換マスタ
route::get('manage/convertFont', [ConvertFontController::class, 'index'])->name('manageConvertFont')->middleware('authManage');
route::get('manage/ConvertFont/edit/{editId?}', [ConvertFontController::class, 'edit'])->name('manageConvertFontEdit')->middleware('authManage');
route::post('manage/ConvertFont/delete/{editId?}', [ConvertFontController::class, 'delete'])->name('manageConvertFontDelete')->middleware('authManage');
route::post('manage/ConvertFont/update', [ConvertFontController::class, 'update'])->name('manageConvertFontUpdate')->middleware('authManage');

// データ一括登録画面
route::get('manage/dataRegister', [DataRegisterController::class, 'index'])->name('manageDataRegister')->middleware('authManage');
route::post('manage/dataRegister/upload', [DataRegisterController::class, 'upload'])->name('manageDataRegisterUpload')->middleware('authManage');

// データ登録変更画面
route::get('manage/dataEdit', [DataEditController::class, 'index'])->name('manageDataEdit')->middleware('authManage');
route::post('manage/dataEdit/search', [DataEditController::class, 'search'])->name('manageDataEditSearch')->middleware('authManage');
route::get('manage/dataEdit/edit/corporation/{editId?}', [DataEditController::class, 'editCorporation'])->name('manageDataEditEditCorporation')->middleware('authManage');
route::get('manage/dataEdit/edit/person/{editId?}', [DataEditController::class, 'editPerson'])->name('manageDataEditEditPerson')->middleware('authManage');
route::post('manage/dataEdit/update/corporation', [DataEditController::class, 'updateCorporation'])->name('manageDataEditUpdateCorporation')->middleware('authManage');
route::post('manage/dataEdit/update/person', [DataEditController::class, 'updatePerson'])->name('manageDataEditUpdatePerson')->middleware('authManage');
route::post('manage/dataEdit/delete/corporation/{editId?}', [DataEditController::class, 'deleteCorporation'])->name('manageDataEditDeleteCorporation')->middleware('authManage');
route::post('manage/dataEdit/delete/person/{editId?}', [DataEditController::class, 'deletePerson'])->name('manageDataEditDeletePerson')->middleware('authManage');

// お問い合わせ画面
route::get('user/contact', [ContactController::class, 'index'])->name('userContact')->middleware('auth');
route::post('user/contact/confirm', [ContactController::class, 'confirm'])->name('userContactConfirm')->middleware('auth');
route::post('user/contact/send', [ContactController::class, 'send'])->name('userContactSend')->middleware('auth');

// 一括検索画面
route::get('user/bulkSearch', [BulkSearchController::class, 'index'])->name('userBulkSearch')->middleware('auth');
route::get('user/bulkSearch/add', [BulkSearchController::class, 'add'])->name('userBulkSearchAdd')->middleware('auth');
route::post('user/bulkSearch/upload', [BulkSearchController::class, 'upload'])->name('userBulkSearchUpload')->middleware('auth');
route::get('user/bulkSearch/confirm', [BulkSearchController::class, 'confirm'])->name('userBulkSearchConfirm')->middleware('auth');
route::get('user/bulkSearch/download', [BulkSearchController::class, 'download'])->name('userBulkSearchDownload')->middleware('auth');
route::get('user/bulkSearch/bulkSearch', [BulkSearchController::class, 'bulkSearch'])->name('userBulkSearchBulkSearch')->middleware('auth');

//　ユーザーログイン画面
route::get('login', [UserLogin::class, 'index'])->name('userLogin');
route::post('login', [UserLogin::class, 'login']);
route::any('logout', [UserLogin::class, 'logout'])->name('userLogout');
route::get('login/auth/{tokenId?}', [UserLogin::class, 'authCode'])->name('userLoginAuth')->middleware('auth');
route::post('login/auth', [UserLogin::class, 'authCodeCheck'])->name('userLoginAuthCheck')->middleware('auth');

// ユーザーホーム画面
route::get('/', [HomeController::class, 'index'])->name('userHome')->middleware('auth');

// 検索画面
route::get('user/search', [SearchController::class, 'index'])->name('userSearch')->middleware('auth');
route::post('user/search/search', [SearchController::class, 'search'])->name('userSearchSearch')->middleware('auth');
route::get('user/search/confirm', [SearchController::class, 'confirm'])->name('userSearchConfirm')->middleware('auth');
route::get('user/search/makePdfSearch', [SearchController::class, 'makePdfSearch'])->name('userSearchMakePdfSearch')->middleware('auth');
route::get('user/search/printSearch', [SearchController::class, 'printSearch'])->name('userSearchPrintSearch')->middleware('auth');

// 利用明細
route::get('user/useReport', [UseReportController::class, 'index'])->name('useReport')->middleware('auth');
route::get('user/useReport/printUseReport', [UseReportController::class, 'printUseReport'])->name('printUseReport')->middleware('auth');


// 請求一覧
route::get('manage/claim', [ClaimController::class, 'index'])->name('manageClaim')->middleware('authManage');
route::get('manage/claim/list', [ClaimController::class, 'list'])->name('manageClaimList')->middleware('authManage');
route::post('manage/claim/search', [ClaimController::class, 'search'])->name('manageClaimSearch')->middleware('authManage');
route::post('manage/claim/claim/{editId?}', [ClaimController::class, 'claim'])->name('manageClaimClaim')->middleware('authManage');
route::post('manage/claim/payment/{editId?}', [ClaimController::class, 'payment'])->name('manageClaimPayment')->middleware('authManage');
route::post('manage/claim/export', [ClaimController::class, 'export'])->name('manageClaimExport')->middleware('authManage');
route::get('manage/claim/edit/{editId?}', [ClaimController::class, 'edit'])->name('manageClaimEdit')->middleware('authManage');
route::post('manage/claim/update/{editId?}', [ClaimController::class, 'update'])->name('manageClaimUpdate')->middleware('authManage');
route::post('manage/claim/pdf/{editId?}', [ClaimController::class, 'pdf'])->name('manageClaimPdf')->middleware('authManage');
route::post('manage/claim/mail/{editId?}', [ClaimController::class, 'mail'])->name('manageClaimMail')->middleware('authManage');


// APIの利用
route::post('api/search', [SearchAPI::class, 'authSearch']);
route::post('api/useReport', [UseReportAPI::class, 'authUseReport']);
