<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Manage\LoginController;
use App\Http\Controllers\Manage\AdminUserController;
use App\Http\Controllers\Manage\AdminHomeController;
use App\Http\Controllers\Manage\UserController;
use App\Http\Controllers\Manage\SearchReportController;
use App\Http\Controllers\Manage\ConvertFontController;
use App\Http\Controllers\Manage\DataRegisterController;
use App\Http\Controllers\Manage\DataEditController;
use App\Http\Controllers\Manage\ClaimController;
use App\Http\Controllers\Manage\UsageStatusController;
use App\Http\Controllers\User\ContactController;
use App\Http\Controllers\User\BulkSearchController;
use App\Http\Controllers\User\CsvBulkSearchController;
use App\Http\Controllers\User\RegistryBulkSearchController;
use App\Http\Controllers\User\LoginController as UserLogin;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\SearchController;
use App\Http\Controllers\User\UseReportController;
use App\Http\Controllers\User\AcurisSearchController;
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
route::get('manage/user/detail/{editId?}/{seqNo?}', [UserController::class, 'detail'])->name('manageUserDetail')->middleware('authManage');
route::get('manage/user/edit/{editId?}/{seqNo?}', [UserController::class, 'edit'])->name('manageUserEdit')->middleware('authManage');
route::post('manage/user/edit/update', [UserController::class, 'update'])->name('manageUserUpdate')->middleware('authManage');
route::post('manage/user/detail/changePassword', [UserController::class, 'changePassword'])->name('manageUserChangePassword')->middleware('authManage');
route::post('manage/user/detail/sendUserInfo', [UserController::class, 'sendUserInfo'])->name('manageUserSendUserInfo')->middleware('authManage');
route::post('manage/user/detail/releaseLogin', [UserController::class, 'releaseLogin'])->name('manageUserReleaseLogin')->middleware('authManage');
route::get('manage/user/contractHistory/{editId?}', [UserController::class, 'contractHistory'])->name('manageUserContractHistory')->middleware('authManage');
route::post('manage/user/edit/contractUpdate', [UserController::class, 'contractUpdate'])->name('manageUserContractUpdate')->middleware('authManage');
route::get('manage/user/contractDelete/{editId?}', [UserController::class, 'contractDelete'])->name('manageUserContractDelete')->middleware('authManage');
route::post('manage/user/delete', [UserController::class, 'deleteUser'])->name('manageUserDeleteUser')->middleware('authManage');

// 月別検索数
route::get('manage/user/searchReport/{editId?}', [SearchReportController::class, 'index'])->name('manageUserSearchReport')->middleware('authManage');
route::post('manage/user/searchReport/search/{editId?}', [SearchReportController::class, 'search'])->name('manageUserSearchReportSearch')->middleware('authManage');
route::post('manage/user/searchReport/pdf/{editId?}', [SearchReportController::class, 'pdf'])->name('manageUserSearchReportPdf')->middleware('authManage');

// 旧字体変換マスタ
route::get('manage/convertFont', [ConvertFontController::class, 'index'])->name('manageConvertFont')->middleware('authManage');
route::get('manage/ConvertFont/edit/{editId?}', [ConvertFontController::class, 'edit'])->name('manageConvertFontEdit')->middleware('authManage');
route::post('manage/ConvertFont/delete/{editId?}', [ConvertFontController::class, 'delete'])->name('manageConvertFontDelete')->middleware('authManage');
route::post('manage/ConvertFont/update', [ConvertFontController::class, 'update'])->name('manageConvertFontUpdate')->middleware('authManage');
route::post('manage/ConvertFont/search', [ConvertFontController::class, 'search'])->name('manageConvertFontSearch')->middleware('authManage');

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

//CSV一括検索画面
route::get('user/csvBulkSearch', [CsvBulkSearchController::class, 'index'])->name('userCsvBulkSearch')->middleware('auth');
route::get('user/csvBulkSearch/add', [CsvBulkSearchController::class, 'add'])->name('userCsvBulkSearchAdd')->middleware('auth');
route::post('user/csvBulkSearch/downloadDelete', [CsvBulkSearchController::class, 'downloadDelete'])->name('userCsvBulkSearchDownloadDelete')->middleware('auth');
route::post('user/csvBulkSearch/upload', [CsvBulkSearchController::class, 'uploadCsv'])->name('userCsvBulkSearchUpload')->middleware('auth');
route::get('user/csvBulkSearch/confirm', [CsvBulkSearchController::class, 'confirm'])->name('userCsvBulkSearchConfirm')->middleware('auth');
route::get('user/csvBulkSearch/bulkSearch', [CsvBulkSearchController::class, 'bulkSearch'])->name('userCsvBulkSearchBulkSearch')->middleware('auth');

//登記簿一括検索画面
route::get('user/registryBulkSearch', [RegistryBulkSearchController::class, 'index'])->name('userRegistryBulkSearch')->middleware('auth');
route::get('user/registryBulkSearch/add', [RegistryBulkSearchController::class, 'add'])->name('userRegistryBulkSearchAdd')->middleware('auth');
route::post('user/registryBulkSearch/downloadDelete', [RegistryBulkSearchController::class, 'downloadDelete'])->name('userRegistryBulkSearchDownloadDelete')->middleware('auth');
route::post('user/registryBulkSearch/upload', [RegistryBulkSearchController::class, 'uploadRegistry'])->name('userRegistryBulkSearchUpload')->middleware('auth');
route::post('user/registryBulkSearch/reUpload', [RegistryBulkSearchController::class, 'reUpload'])->name('userRegistryBulkSearchReUpload')->middleware('auth');
route::get('user/registryBulkSearch/confirm', [RegistryBulkSearchController::class, 'confirm'])->name('userRegistryBulkSearchConfirm')->middleware('auth');
route::get('user/registryBulkSearch/download', [RegistryBulkSearchController::class, 'download'])->name('userRegistryBulkSearchDownload')->middleware('auth');
route::get('user/registryBulkSearch/bulkSearch', [RegistryBulkSearchController::class, 'bulkSearch'])->name('userRegistryBulkSearchBulkSearch')->middleware('auth');

//一括検索
route::get('user/bulkSearch/result/{batchId}/{type}', [BulkSearchController::class, 'downloadResult'])->name('userBulkSearchResult')->middleware('auth');

// ユーザーログイン画面
route::get('login', [UserLogin::class, 'index'])->name('userLogin');
route::post('login', [UserLogin::class, 'login']);
route::any('logout', [UserLogin::class, 'logout'])->name('userLogout');
route::get('login/auth/{tokenId?}', [UserLogin::class, 'authCode'])->name('userLoginAuth');
route::post('login/auth', [UserLogin::class, 'authCodeCheck'])->name('userLoginAuthCheck');

// ユーザーホーム画面
route::get('/', [HomeController::class, 'index'])->name('userHome')->middleware('auth');

// 検索画面
route::get('user/search', [SearchController::class, 'index'])->name('userSearch')->middleware('auth');
route::post('user/search/checkDeposit', [SearchController::class, 'checkDeposit'])->name('userSearchCheckDeposit')->middleware('auth');
route::get('user/search/search', [SearchController::class, 'search'])->name('userSearchSearch')->middleware('auth');
route::get('user/search/confirm', [SearchController::class, 'confirm'])->name('userSearchConfirm')->middleware('auth');
route::get('user/search/makePdfSearch', [SearchController::class, 'makePdfSearch'])->name('userSearchMakePdfSearch')->middleware('auth');
route::get('user/search/printSearch', [SearchController::class, 'printSearch'])->name('userSearchPrintSearch')->middleware('auth');

// 利用明細
route::get('user/useReport', [UseReportController::class, 'index'])->name('useReport')->middleware('auth');
route::post('user/useReport/search', [UseReportController::class, 'search'])->name('useReportSearch')->middleware('auth');
route::post('user/useReport/printUseReport', [UseReportController::class, 'printUseReport'])->name('printUseReport')->middleware('auth');


// 請求一覧
route::get('manage/claim', [ClaimController::class, 'index'])->name('manageClaim')->middleware('authManage');
route::get('manage/claim/list', [ClaimController::class, 'list'])->name('manageClaimList')->middleware('authManage');
route::post('manage/claim/search', [ClaimController::class, 'search'])->name('manageClaimSearch')->middleware('authManage');
route::post('manage/claim/claim/{editId?}', [ClaimController::class, 'claim'])->name('manageClaimClaim')->middleware('authManage');
route::post('manage/claim/payment/{editId?}', [ClaimController::class, 'payment'])->name('manageClaimPayment')->middleware('authManage');
route::post('manage/claim/notClaim/{editId?}', [ClaimController::class, 'notClaim'])->name('manageClaimNotClaim')->middleware('authManage');
route::post('manage/claim/notPayment/{editId?}', [ClaimController::class, 'notPayment'])->name('manageClaimNotPayment')->middleware('authManage');
route::post('manage/claim/export', [ClaimController::class, 'export'])->name('manageClaimExport')->middleware('authManage');
route::post('manage/claim/bulkMail', [ClaimController::class, 'bulkMail'])->name('manageClaimBulkMail')->middleware('authManage');
route::post('manage/claim/bulkClaim', [ClaimController::class, 'bulkClaim'])->name('manageClaimBulkClaim')->middleware('authManage');
route::get('manage/claim/edit/{editId?}', [ClaimController::class, 'edit'])->name('manageClaimEdit')->middleware('authManage');
route::post('manage/claim/update/{editId?}', [ClaimController::class, 'update'])->name('manageClaimUpdate')->middleware('authManage');
route::post('manage/claim/tempSave/{editId?}', [ClaimController::class, 'tempSave'])->name('manageClaimTempSave')->middleware('authManage');
route::post('manage/claim/pdf/{editId?}', [ClaimController::class, 'pdf'])->name('manageClaimPdf')->middleware('authManage');
route::post('manage/claim/mail/{editId?}', [ClaimController::class, 'mail'])->name('manageClaimMail')->middleware('authManage');
route::post('manage/claim/delete', [ClaimController::class, 'delete'])->name('manageClaimDelete')->middleware('authManage');

// 利用状況一覧
route::get('manage/usageStatus', [UsageStatusController::class, 'index'])->name('manageUsageStatus')->middleware('authManage');
route::post('manage/usageStatus/search', [UsageStatusController::class, 'search'])->name('manageUsageStatusSearch')->middleware('authManage');
route::get('manage/usageStatus/detail/{editId?}', [UsageStatusController::class, 'detail'])->name('manageUsageStatusDetail')->middleware('authManage');
route::get('manage/usageStatus/listCsv', [UsageStatusController::class, 'listCsv'])->name('manageUsageStatusListCsv')->middleware('authManage');
route::post('manage/usageStatus/listPdf', [UsageStatusController::class, 'listPdf'])->name('manageUsageStatusListPdf')->middleware('authManage');
route::post('manage/usageStatus/detailPdf/{editId?}', [UsageStatusController::class, 'detailPdf'])->name('manageUsageStatusDetailPdf')->middleware('authManage');

// 海外検索画面
route::get('user/AcurisSearch/note', [AcurisSearchController::class, 'note'])->name('userAcurisSearchNote')->middleware('auth');
route::get('user/AcurisSearch', [AcurisSearchController::class, 'index'])->name('userAcurisSearch')->middleware('auth');
route::post('user/AcurisSearch/search', [AcurisSearchController::class, 'search'])->name('userAcurisSearchSearch')->middleware('auth');
route::get('user/AcurisSearch/result', [AcurisSearchController::class, 'result'])->name('userAcurisSearchResult')->middleware('auth');
route::get('user/AcurisSearch/print', [AcurisSearchController::class, 'print'])->name('userAcurisSearchPrint')->middleware('auth');
route::get('user/AcurisSearch/pdf', [AcurisSearchController::class, 'pdf'])->name('userAcurisSearchPdf')->middleware('auth');
route::get('user/AcurisSearch/excel', [AcurisSearchController::class, 'excel'])->name('userAcurisSearchExcel')->middleware('auth');
route::post('user/AcurisSearch/lookupPdf', [AcurisSearchController::class, 'lookupPdf'])->name('userAcurisSearchLookupPdf')->middleware('auth');

// APIの利用
route::post('api/search', [SearchAPI::class, 'authSearch']);
route::post('api/useReport', [UseReportAPI::class, 'authUseReport']);
