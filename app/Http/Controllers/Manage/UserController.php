<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * ユーザー管理画面
 */
class UserController extends Controller
{

    // 初期表示
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        return view('manage/user/list', []);

    }

}
