<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;

/**
 * データ登録変更画面
 */
class DataEditController extends Controller
{

    public function index() {
        return view('manage/dataEdit/list', []);
    }
}
