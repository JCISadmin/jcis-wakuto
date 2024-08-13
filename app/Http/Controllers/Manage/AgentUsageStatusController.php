<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MContractStatus;
use App\Models\MContractPlan;
use App\Models\AgentUsageStatus;
use App\Models\MUserCompany;
use App\Models\CsvAgentUsageStatus;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use DateTime;
use Illuminate\Support\Facades\Crypt;

/**
 * 代理店利用状況一覧
 */
class AgentUsageStatusController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        // 検索条件
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['agentNo'] = 1;
            $cond['targetMonth'] = now()->format('Y-m');
            $cond['contractPlan'] = '';
            $cond['dispType'] = 2;
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
        $model = new AgentUsageStatus();

        $startOfMonth = new DateTime($cond['targetMonth'] . '-01');
        $endOfMonth = (clone $startOfMonth)->modify('last day of this month');
        $startDate = $startOfMonth->format('Y-m-d 00:00:00');
        $endDate = $endOfMonth->format('Y-m-d 23:59:59');

        // 利用状況一覧データ取得
        $userList = $model->getList(
            $pageNum,
            $cond['contractPlan'],
            "",
            $cond['dispType'],
            $startDate,
            $endDate,
            true,
            $cond['agentNo']
        );

        $assignAry = [
            'agentNo' => $cond['agentNo'],
            'targetMonth' => $cond['targetMonth'],
            'contractPlan' => $cond['contractPlan'],
            'dispType' => $cond['dispType'],
            'userList' => $userList,
            'selectList' => [
                'contractStatus' => $contractStatusModel->getSelectList(),
                'contractPlan' => $contractPlanModel->getSelectList(),
                'agent' => config('agent.agentList')
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/agentUsageStatus/list', $assignAry);
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
        $request->session()->put('agentNo', $cond['agentNo']);

        return redirect()->route('manageAgentUsageStatus');
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
            $cond['agentNo'] = 1;
            $cond['targetMonth'] = now()->format('Y-m');
            $cond['contractPlan'] = '';
            $cond['dispType'] = 2;
        }

        // 会社ID復号
        $companyId = Crypt::decrypt($editId);

        $startOfMonth = new DateTime($cond['targetMonth'] . '-01');
        $endOfMonth = (clone $startOfMonth)->modify('last day of this month');
        $startDate = $startOfMonth->format('Y-m-d 00:00:00');
        $endDate = $endOfMonth->format('Y-m-d 23:59:59');

        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        $model = new AgentUsageStatus();
        $detail = $model->getDetailData($companyId, $startDate, $endDate);

        $assignAry = [
            'companyName' => $companyName,
            'userIdList' => $detail['userIdList'],
            'totalSearchCount' => $detail['totalSearchCount'],
            'totalDupSearchCount' => $detail['totalDupSearchCount'],
            'pageNo' => $request->session()->get(__CLASS__ . 'pageNo'),
        ];

        return view('manage/agentUsageStatus/detail',$assignAry);
    }

    /**
     * CSV出力(利用状況一覧)
     *
     * @param Request $request
     * @return BinaryFileResponse
     */
    public function listCsv(Request $request): BinaryFileResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['agentNo'] = 1;
            $cond['targetMonth'] = now()->format('Y-m');
            $cond['contractPlan'] = '';
            $cond['dispType'] = 2;
        }

        //ページ行数保持
        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new CsvAgentUsageStatus();

        $csvInfo = $model->makeCsv($cond, $pageNum);
        $headers = [['Content-Type' => 'text/css']];

        return response()->download($csvInfo['filePath'], $csvInfo['fileName'], $headers)->deleteFileAfterSend(true);

    }
}
