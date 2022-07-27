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

        //現在日時
        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        //会社名
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        //表示データ取得
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');
        $model = new Report();

        $pageInfo = null;
        $pageData= null;

        //全件指定
        if($cond['dispType'] === 'all'){

            $pageInfo = $model->getReportPageInfo($companyId, $pageNo, 'user');
            $pageData = $pageInfo['pageData'];
            $pageAry = $pageData->items();
            $pageItem = array_values($pageAry);

            $year = null;
            if(!empty($pageItem)){
                $year = $pageItem[0];
            }
    
            $data = $model->getReportData($companyId, $year);

            //月別情報が1つも無い場合、表を非表示
            if(empty($data['month'])){
                $detail = null;
            }else{
                $detail = [
                    'month' => $data['month'],
                    'year' => $data['year'],
                ];
            }

        //月別指定
        }elseif($cond['dispType'] === 'month'){

            $useMonth = new DateTime($cond['useMonth']);
            //指定月が現在より先
            if($now < $useMonth){
                $detail = null;
            }else{
                $useY = $useMonth->format('Y');
                $useYM = $useMonth->format('Y-m');

                $data = $model->getReportData($companyId, $useY);

                //指定月情報が一つも無い場合、表を非表示
                if(!isset($data['month'][$useY][$useYM])){
                    $detail = null;
                }else{

                    $detail['month'][$useY][$useYM] = $data['month'][$useY][$useYM];
                    $detail['year'][$useY] = $data['year'][$useY];
                }
            }
        }

        //デポジット情報
        $webDeposit = 0;
        $webUnitPrice = 0;
        $webRemainCount = 0;
        $apiDeposit = 0;
        $apiUnitPrice = 0;
        $apiRemainCount = 0;

        $tContractPlan = new TContractPlan();
        $webPlan = $tContractPlan->getPlan($companyId, self::TYPE_WEB);
        $apiPlan = $tContractPlan->getPlan($companyId, self::TYPE_API);
        //DBデポジット
        if(!is_null($webPlan)){
            $webDeposit = is_null($webPlan['deposit']) ? 0 : $webPlan['deposit'];
            $webUnitPrice = is_null($webPlan['contractDetail']['searchUnitPrice']) ? 0 : $webPlan['contractDetail']['searchUnitPrice'];
            $webRemainCount = $webDeposit / $webUnitPrice;
        }
        //APIデポジット
        if(!is_null($apiPlan)){      
            $apiDeposit = is_null($apiPlan['deposit']) ? 0 : $apiPlan['deposit'];
            $apiUnitPrice = is_null($apiPlan['contractDetail']['searchUnitPrice']) ? 0 : $apiPlan['contractDetail']['searchUnitPrice'];
            $apiRemainCount = $apiDeposit / $apiUnitPrice;
        }
            
        //今月検索件数/年間検索件数/デポジット検索欄
        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $depositList['web'] = [];
        $depositList['api'] = [];
        $nowData = $model->getReportData($companyId, $now->format('Y'));

        if(isset($nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
            $monthSearchCount = $nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
        }
        if(isset($nowData['year'][$now->format('Y')]['totalSearchCount'])){
            $yearSearchCount = $nowData['year'][$now->format('Y')]['totalSearchCount'];
        }
        if(isset($data['deposit'])){
            $depositList = $data['deposit'];
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
            'pageList' => $pageData,
            'detail' => $detail,
            'useMonth' => $cond['useMonth'],
            'dispType' => $cond['dispType'],
            'pageNo' => $pageNo,
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
        if($cond['dispType'] === 'month' && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        //月別指定 かつ 指定月が現在よりも先
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
        $companyId = auth()->user()->companyId;
        $fileName = $model->getFileName();

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['dispType'] = 'all';
            $cond['useMonth'] = '';
        }
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        $string = $model->makePdf($fileName, $companyId, $cond['dispType'], $cond['useMonth'], $pageNo);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
   }


}
