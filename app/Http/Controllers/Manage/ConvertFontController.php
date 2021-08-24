<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;

/**
 * 旧字体変換マスタ
 */
class ConvertFontController extends Controller
{
    public function index() {
        return view('manage/convertFont/list', []);
    }
}
