<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MPrefecture;
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
        $this->actionLog(__CLASS__, __FUNCTION__);
        $model = new MPrefecture();

        $assignAry = [
            'selectList' => [
                'prefecture' => $model->getSelectList(),
            ],
        ];

        return view('user/search/edit', $assignAry);
    }

}
