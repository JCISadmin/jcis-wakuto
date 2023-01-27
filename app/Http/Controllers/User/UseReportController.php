<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\UseReport;
use Illuminate\Http\RedirectResponse;
use DateTime;
use App\Models\BaseModel;

/**
 * 利用明細画面
 */
class UseReportController extends Controller
{

    /**
     * 初期画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new UseReport();

        $companyId = auth()->user()->companyId;

        // 検索条件
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['dispType'] = BaseModel::DISP_TYPE_ALL;
            $cond['useMonth'] = '';
        }

        // ページネーション
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        // 表示データ取得
        $data = $model->getData($companyId, $pageNo, $cond['dispType'], $cond['useMonth']);

        $assignAry = [
            'companyId' => $companyId,
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

        return view('user/useReport/list', $assignAry);
    }

    /**
     * 検索
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);

        //月別指定 かつ 月の入力無し
        if($cond['dispType'] === BaseModel::DISP_TYPE_MONTH && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        //月別指定 かつ 指定月が現在よりも先
        if($cond['dispType'] === BaseModel::DISP_TYPE_MONTH && new DateTime() < new DateTime($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '表示データがありません。']);
        }

        return redirect()->route('useReport');
    }

    /**
    * 利用明細PDF
    *
    * @param Request $request
    * @return string
    */
    public function printUseReport(Request $request): string
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $companyId = auth()->user()->companyId;

        $model = new UseReport();

        // 検索条件
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['dispType'] = BaseModel::DISP_TYPE_ALL;
            $cond['useMonth'] = '';
        }

        // ページネーション
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        // PDFファイル名取得
        $fileName = $model->getFileName();

        // PDF生成
        $string = $model->makePdf($fileName, $companyId, $pageNo, $cond['dispType'], $cond['useMonth']);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
    }

}
