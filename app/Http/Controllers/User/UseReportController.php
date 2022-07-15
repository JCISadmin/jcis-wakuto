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
use App\Models\MUserCompany;
use App\Models\Report;
use App\Models\TContractPlan;

/**
 * 利用明細画面
 */
class UseReportController extends Controller
{

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';

    /**
     * 初期画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['dispType'] = 'all';
            $cond['useMonth'] = '';
        }

        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;

        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        $model = new Report();
        if($cond['dispType'] === 'all'){
            $detail = $model->getReportData($companyId);
        }elseif($cond['dispType'] === 'month'){
            $detail = $model->getReportData($companyId, true, $cond['useMonth']);
        }

        $tContractPlan = new TContractPlan();
        $webPlan = $tContractPlan->getPlan($companyId, self::TYPE_WEB);
        $apiPlan = $tContractPlan->getPlan($companyId, self::TYPE_API);

        $webDeposit = 0;
        $webUnitPrice = 0;
        $webRemainCount = 0;
        $apiDeposit = 0;
        $apiUnitPrice = 0;
        $apiRemainCount = 0;
        if(!is_null($webPlan)){
            $webDeposit = is_null($webPlan['deposit']) ? 0 : $webPlan['deposit'];
            $webUnitPrice = is_null($webPlan['contractDetail']['searchUnitPrice']) ? 0 : $webPlan['contractDetail']['searchUnitPrice'];
            $webRemainCount = $webDeposit / $webUnitPrice;
        }
        if(!is_null($apiPlan)){      
            $apiDeposit = is_null($apiPlan['deposit']) ? 0 : $apiPlan['deposit'];
            $apiUnitPrice = is_null($apiPlan['contractDetail']['searchUnitPrice']) ? 0 : $apiPlan['contractDetail']['searchUnitPrice'];
            $apiRemainCount = $apiDeposit / $apiUnitPrice;
        }

        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $depositList['web'] = [];
        $depositList['api'] = [];

        if(!is_null($detail)){

            $nowDetail = $model->getReportData($companyId);
            if(isset($nowDetail['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
                $monthSearchCount = $nowDetail['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
            }
            if(isset($nowDetail['year'][$now->format('Y')]['totalSearchCount'])){
                $yearSearchCount = $nowDetail['year'][$now->format('Y')]['totalSearchCount'];
            }
            $depositList = $detail['deposit'];
        }

        $assignAry = [
            'date' => $date,
            'companyName' => $companyName,
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'webDeposit' => $webDeposit,
            'apiDeposit' => $apiDeposit,
            'webRemainCount' => $webRemainCount,
            'apiRemainCount' => $apiRemainCount,
            'depositList' => $depositList,
            'detail' => $detail,
            'useMonth' => $cond['useMonth'],
            'dispType' => $cond['dispType'],
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

        if($cond['dispType'] === 'month' && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        if($cond['dispType'] === 'month' && new DateTime() < new DateTime($cond['useMonth'])){
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

        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;
        $fileName = $model->getFileName();
        $string = $model->makePdf($companyId, $userId, $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
   }


}
