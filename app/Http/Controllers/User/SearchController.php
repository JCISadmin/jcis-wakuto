<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\MPrefecture;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\User\Search\SearchRequest;
use App\Models\SearchEngine;

/**
 * WEB検索画面
 */
class SearchController extends Controller
{

    /**
     * 初期画面
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
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

    public function search(SearchRequest $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new SearchEngine();
        $aa = $model->searchCompany('法人2', '東京都' ,true);

        dump($aa);
    }


}
