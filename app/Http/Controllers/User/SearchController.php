<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * WEB検索画面
 */
class SearchController extends Controller
{

    /**
     * 初期画面表示
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        return view('user/search/edit', []);
    }

}
