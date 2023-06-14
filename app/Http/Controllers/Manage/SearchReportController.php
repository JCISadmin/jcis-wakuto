<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\BaseModel;
use App\Models\SearchReport;
use App\Http\Requests\Manage\User\SearchReport\SearchRequest;
use DateTime;

/**
 * 月別検索数画面
 */
class SearchReportController extends Controller
{
    /**
     * 月別検索数画面 画面表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function index(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new SearchReport();

        // 検索条件
        $cond = $request->session()->get(__CLASS__ . 'searchReport');
        if (empty($cond)) {
            $cond['dispType'] = BaseModel::DISP_TYPE_ALL;
            $cond['useMonth'] = '';
        }

        // ページネーション
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        // 表示データ取得
        $data = $model->getData($editId, $pageNo, $cond['dispType'], $cond['useMonth']);

        $assignAry = [
            'companyId' => $editId,
            'dispType' => $cond['dispType'],
            'useMonth' => $cond['useMonth'],
            'date' => $data['date'],
            'companyName' => $data['companyName'],
            'monthSearchCount' => $data['monthSearchCount'],
            'yearSearchCount' => $data['yearSearchCount'],
            'webDepositInfo' => $data['webDepositInfo'],
            'apiDepositInfo' => $data['apiDepositInfo'],
            'depositList' => $data['depositList'],
            'pageList' => $data['pageList'],
            'detail' => $data['detail'],
            'pageNo' => $data['pageNo'],
        ];

        return view('manage/user/searchReport', $assignAry);
    }

    /**
     * 月別検索数画面 検索
     *
     * @param SearchRequest $request
     * @param string $editId
     * @return RedirectResponse
     */
    public function search(SearchRequest $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'searchReport', $cond);

        //月別指定 かつ 月の入力無し
        if($cond['dispType'] === BaseModel::DISP_TYPE_MONTH && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        //月別指定 かつ 指定月が現在よりも先
        if($cond['dispType'] === BaseModel::DISP_TYPE_MONTH && new DateTime() < new DateTime($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '表示データがありません。']);
        }

        return redirect()->route('manageUserSearchReport', ['editId' => $editId]);
    }

    /**
     * 月別検索数PDFの生成
     *
     * @param Request $request
     * @param $editId
     * @return string
     */
    public function pdf(Request $request, $editId): string
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new SearchReport();

        // 検索条件
        $cond = $request->session()->get(__CLASS__ . 'searchReport');
        if (empty($cond)) {
            $cond['dispType'] = BaseModel::DISP_TYPE_ALL;
            $cond['useMonth'] = '';
        }

        // ページネーション
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        // PDFファイル名取得
        $fileName = $model->getFileName($editId);

        // PDF生成
        $string = $model->makePdf($fileName, $editId, $pageNo, $cond['dispType'], $cond['useMonth']);

        return $string;
    }

}
