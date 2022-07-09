<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MContractStatus;
use App\Models\MContractPlan;
use App\Models\UsageStatus;
use App\Models\MUserCompany;
use App\Models\Report;


/**
 * 管理ユーザー一覧
 */
class UsageStatusController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['searchDateFrom'] = '';
            $cond['searchDateTo'] = '';
            $cond['contractPlan'] = '';
            $cond['chargeName'] = '';
            $cond['dispType'] = 1;
        }

        //ページ行数保持
        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        // ページ番号保持
        $pageNo = $request->input('page', '');
        $request->session()->put(__CLASS__ . 'pageNo', $pageNo);

        $contractStatusModel = new MContractStatus();
        $contractPlanModel = new MContractPlan();
        $model = new UsageStatus();

        $userList = $model->getList(
            '',
            '',
            $cond['contractPlan'],
            '',
            $pageNum,
            $cond['chargeName'],
            $cond['dispType'],
            $cond['searchDateFrom'],
            $cond['searchDateTo'],
        );

        $assignAry = [
            'searchDateFrom' => $cond['searchDateFrom'],
            'searchDateTo' => $cond['searchDateTo'],
            'contractPlan' => $cond['contractPlan'],
            'chargeName' => $cond['chargeName'],
            'dispType' => $cond['dispType'],
            'userList' => $userList,
            'sumSearchCount' => $userList->sumSearchCount,
            'sumPrice' => $userList->sumPrice,
            'selectList' => [
                'contractStatus' => $contractStatusModel->getSelectList(),
                'contractPlan' => $contractPlanModel->getSelectList(),
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/usageStatus/list', $assignAry);
    }

    /**
     * 検索アクション
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);
        $request->session()->put(__CLASS__ . 'pageNo', '');

        return redirect()->route('manageUsageStatus');
    }

    /**
     * 詳細
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function detail(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['searchDateFrom'] = '';
            $cond['searchDateTo'] = '';
            $cond['contractPlan'] = '';
            $cond['chargeName'] = '';
            $cond['dispType'] = 1;
        }


        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($editId);

        $model = new UsageStatus();
        $detail = $model->getReportDataByPeriod($editId, $cond['searchDateFrom'], $cond['searchDateTo']);

        $assignAry = [
            'companyId' => $editId,
            'companyName' => $companyName,
            'detail' => $detail,
            'useMonth' => '',
            'dispType' => 'all',
            'pageNo' => $request->session()->get(__CLASS__ . 'pageNo'),
        ];

        return view('manage/usageStatus/detail',$assignAry);
    }
    
    /**
     * PDFの生成
     *
     * @param Request $request
     * @param $editId
     * @return string
     */
    public function pdf(Request $request, $editId): string
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        
        $model = new UsageStatus();
        
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['searchDateFrom'] = '';
            $cond['searchDateTo'] = '';
            $cond['contractPlan'] = '';
            $cond['chargeName'] = '';
            $cond['dispType'] = 1;
        }

        $fileName = $model->getFileName($editId);
        $string = $model->makePdf($fileName, $editId, $cond['searchDateFrom'], $cond['searchDateTo']);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"$fileName\"");

        return $string;
    }

}
