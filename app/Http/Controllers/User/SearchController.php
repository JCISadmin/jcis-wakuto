<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use App\Models\MPrefecture;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\User\Search\SearchRequest;
use App\Models\SearchEngine;
use App\Models\TContractPlan;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * WEB検索画面
 */
class SearchController extends Controller
{

    // スマホ版サイトを表示させるユーザーエージェント一覧
    private array $mobileList = ['iPhone', 'iPod', 'Android', 'Windows Phone', 'Mobile'];

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
     * デポジット残高確認
     *
     * @param SearchRequest $request
     * @return Application|Factory|View|RedirectResponse
     * @noinspection PhpPossiblePolymorphicInvocationInspection
     */
    public function checkDeposit(SearchRequest $request): View|Factory|RedirectResponse|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new TContractPlan();
        $companyId = auth()->user()->companyId;
        $contractPlanId = auth()->user()->contractPlanId;

        $request->session()->put(__CLASS__ . 'editData', $request->input());

        $companyKeywords = $request->input('companyName');
        $parsonKeywords = $request->input('parsonName');

        $companyCount = count(array_diff($companyKeywords, [""]));
        $parsonCount = count(array_diff($parsonKeywords, [""]));
        $count = $companyCount + $parsonCount;

        if ($companyId == 'admin') {
            $isEnough = true;
        } else {
            $isEnough = $model->checkDeposit($companyId, $contractPlanId, $count);
        }

        if (!$isEnough) {
            return view('user/search/askDebit');
        } else {
            return redirect()->route('userSearchSearch');
        }
    }

    /**
     * WEB検索
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function search(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        /** @var AuthUser $user */
        $user = auth()->user();
        $model = new SearchEngine();

        $data = $request->session()->get(__CLASS__ . 'editData');

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
        if ($data['fuzzyFlg'] === 'true') {
            $isFussy = true;
        }

        $keyword = [];
        $result = [];
        foreach($data['companyName'] as $item) {
            if ($item !== '') {

                $list = $model->searchCompany($user->companyId, $user->contractPlanId, $user->userId, $item, $prefCity, $isFussy, true);
                foreach ($list as $value) {
                    if (is_array($value)) {
                        $value['searchType'] = "company";
                        $result[] = $value;
                    }
                }

                if (count($list) > 0) {
                    $keyword['company']['exist'][$item] = $item;
                } else {
                    $keyword['company']['noExist'][$item] = $item;
                }
            }
        }

        foreach($data['parsonName'] as $item) {
            if ($item !== '') {

                $list = $model->searchPerson($user->companyId, $user->contractPlanId, $user->userId, $item, $age, $prefCity, $isFussy, '', true);
                foreach ($list as $value) {
                    if (is_array($value)) {
                        $value['searchType'] = "person";
                        $result[] = $value;
                    }
                }

                if (count($list) > 0) {
                    $keyword['person']['exist'][$item] = $item;
                } else {
                    $keyword['person']['noExist'][$item] = $item;
                }
            }
        }

        $collection = collect($result);
        $searchData = [
            'keyword' => $keyword,
            'result' => $collection,
            'searchTime' => date("Y/m/d h:i"),
        ];

        $request->session()->put(__CLASS__ . 'searchData', $searchData);
        $request->session()->put(__CLASS__ . 'pageLine', 10);

        return redirect()->route('userSearchConfirm');
    }

    /**
     * 検索結果画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function confirm(Request $request): View|Factory|Application {

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $searchDataResult = $searchData['result'];
        $searchDataKeyword = $searchData['keyword'];
        $searchDataSearchTime = $searchData['searchTime'];

        $pageNum = is_null($request->input('page')) ? 1 : $request->input('page');
        if (!is_null($request->input('pageLine'))) {
            $pageLine = $request->input('pageLine');
        } else if (!is_null($request->session()->get(__CLASS__ . 'pageLine'))) {
            $pageLine = $request->session()->get(__CLASS__ . 'pageLine');
        } else {
            $pageLine = 10;
        }

        $page = new LengthAwarePaginator(
            $searchDataResult->forPage($pageNum, $pageLine),
            count($searchDataResult),
            $pageLine, // 1ページ行数
            $pageNum, // ページ番号
            array('path' => 'confirm'),
        );

        $viewData = ($page->toArray())['data'];

        $assignAry = [
            'pageNum' => $pageNum,
            'pageLine' => $pageLine,
            'pageNateModel' => $page,
            'keyword' => $searchDataKeyword,
            'searchTime' => $searchDataSearchTime,
            'result' => $viewData,
        ];

        $request->session()->put(__CLASS__.'pageLine', $pageLine);

        $viewPath = 'user/search/confirm';

        $userAgent = $request->header('User-Agent');
        foreach ($this->mobileList as $mobileName) {
            if (str_contains($userAgent, $mobileName)) {
                $viewPath = 'user/search/confirmMobile';
                break;
            }
        }

        return view($viewPath, $assignAry);
    }

    /**
     * 計算結果 PDF出力
     *
     * @param Request $request
     * @return string
     */
    public function makePdfSearch(Request $request): string
    {

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $pdfData = [
            'keyword' => $searchData['keyword'],
            'searchTime' => $searchData['searchTime'],
            'result' => $searchData['result'],
        ];

        $model = new SearchEngine();
        $fileName = $model->getFileName();
        $string = $model->makePdf($pdfData, $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        return $string;
    }

    /**
     * 計算結果 印刷用html表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function printSearch(Request $request): View|Factory|Application
    {

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $assignAry = [
            'keyword' => $searchData['keyword'],
            'searchTime' => $searchData['searchTime'],
            'result' => $searchData['result'],
        ];

        return view('user/search/confirmPrint', $assignAry);
    }
}
