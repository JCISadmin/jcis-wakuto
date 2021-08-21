<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MAdminUser;
use App\Http\Requests\Manage\AdminUser\SearchRequest;
use App\Http\Requests\Manage\AdminUser\UpdateRequest;
use Illuminate\Support\Facades\Validator;

/**
 * 管理ホーム画面
 */
class AdminHomeController extends Controller
{

    /**
     * 初期商事
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request) {


        return view('manage/home');
    }


}
