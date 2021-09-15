<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use App\Models\MPrefecture;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\User\Search\SearchRequest;
use App\Models\SearchEngine;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $request->session()->flash(__CLASS__ . 'searchData');

        $assignAry = [
            'selectList' => [
                'prefecture' => $model->getSelectList(),
            ],
        ];

        return view('user/search/edit', $assignAry);
    }

    /**
     * WEB検索
     *
     * @param SearchRequest $request
     */
    public function search(SearchRequest $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        /** @var AuthUser $user */
        $user = auth()->user();
        $model = new SearchEngine();

        $data = $request->input();

        $prefCity = '';
        if (is_null($data['prefecture']) === false) {
            $prefCity = $data['prefecture'];
            if (is_null($data['city']) === false) {
                $prefCity .= $data['city'];
            }
        }

        $age = '';
        if (is_null($data['age']) === false) {
            $age = $data['age'];
        }

        $isFussy = false;
        if (isset($data['fuzzyFlg'])) {
            $isFussy = true;
        }

        $keyword = [];
        $result = [];
        foreach($data['companyName'] as $item) {
            if ($item !== '') {
                $keyword[$item] = $item;
                $list = $model->searchCompany($user->companyId, $user->contractPlanId, $user->userId, $item, $prefCity, $isFussy);
                foreach ($list as $value) {
                    $result[] = $value;
                }
            }
        }

        foreach($data['parsonName'] as $item) {
            if ($item !== '') {
                $keyword[$item] = $item;
                $list = $model->searchPerson($user->companyId, $user->contractPlanId, $user->userId, $item, $age, $prefCity, $isFussy, '');
                foreach ($list as $value) {
                    $result[] = $value;
                }
            }
        }

        $collection = collect($result);
        $searchData = [
            'keyword' => $keyword,
            'result' => $collection
        ];

        $request->session()->put(__CLASS__ . 'searchData', $searchData);

        // TODO ページャーの生成サンプル
        $page = new LengthAwarePaginator(
            $collection->forPage(1, 3), // データ分割　forPage($request->page, 5)が良い？　引数は、ページ番号、1ページ行数
            count($collection),
            3, // 1ページ行数
            1, // ページ番号
            array('path' => $request->url())
        );
        dump($page);







    }


}
