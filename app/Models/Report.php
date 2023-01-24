<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * レポート機能モデル(共通)
 */
class Report extends BaseModel
{
    use HasFactory;

    /**
     * 月別検索数 データ取得
     *
     * @param $companyId
     * @param $pageNo
     * @param $dispType
     * @param $useMonth
     * @return array
     */
    public function getData($companyId, $pageNo, $dispType, $useMonth) : array
    {
        //現在日時
        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        //会社名
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        // ログインユーザー
        $user = auth()->user();
        $loginUser = $user->companyId;

        // ページネーション情報取得
        $pageData = $this->getReportPageData($companyId, $pageNo, $loginUser);

        //全件指定
        if($dispType === BaseModel::DISP_TYPE_ALL){

            // ページネーション指定年
            $year = $this->getYearByPagination($pageData);

            // ページネーション指定年の表示データ取得
            $reportData = $this->getReportData($companyId, $year);

            //月別情報が1つも無い場合 表を非表示
            if(empty($reportData['month'])){
                $detail = null;
            }else{
                $detail = [
                    'month' => $reportData['month'],
                    'year' => $reportData['year'],
                ];
            }

        //月別指定
        }elseif($dispType === BaseModel::DISP_TYPE_MONTH){

            $useMonth = new DateTime($useMonth);
            $useY = $useMonth->format('Y');
            $useYM = $useMonth->format('Y-m');

            // 画面入力指定年
            $year = $useY;

            // 画面入力指定年の表示データ取得
            $reportData = $this->getReportData($companyId, $year);

            if($now < $useMonth){
                //指定月が現在より先の場合 表を非表示
                $detail = null;
            }else{
                //指定月情報が一つも無い場合 表を非表示
                if(!isset($reportData['month'][$useY][$useYM])){
                    $detail = null;
                }else{
                    $detail['month'][$useY][$useYM] = $reportData['month'][$useY][$useYM];
                    $detail['year'][$useY] = $reportData['year'][$useY];
                }
            }
        }

        //デポジット情報
        $webDepositInfo = [
            'deposit' => 0,
            'remainCount' => 0,
        ];
        $apiDepositInfo = [
            'deposit' => 0,
            'remainCount' => 0,
        ];

        $tContractPlan = new TContractPlan();
        $webPlan = $tContractPlan->getPlan($companyId, BaseModel::PLAN_TYPE_WEB);
        $apiPlan = $tContractPlan->getPlan($companyId, BaseModel::PLAN_TYPE_API);
        //DBデポジット
        if(!is_null($webPlan)){
            $webDepositInfo['deposit'] = $webPlan['deposit'];
            $webUnitPrice = empty($webPlan['contractDetail']['searchUnitPrice']) ? 1 : $webPlan['contractDetail']['searchUnitPrice'];
            $webDepositInfo['remainCount'] = ceil($webDepositInfo['deposit'] / $webUnitPrice);
        }
        //APIデポジット
        if(!is_null($apiPlan)){      
            $apiDepositInfo['deposit'] = $apiPlan['deposit'];
            $apiUnitPrice = empty($apiPlan['contractDetail']['searchUnitPrice']) ? 1 : $apiPlan['contractDetail']['searchUnitPrice'];
            $apiDepositInfo['remainCount'] = ceil($apiDepositInfo['deposit'] / $apiUnitPrice);
        }

        //今月検索件数/年間検索件数/デポジット検索欄
        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $depositList['web'] = [];
        $depositList['api'] = [];

        // 今年・今月 の情報を取得
        if( isset($reportData['year'][$now->format('Y')]) && isset($reportData['month'][$now->format('Y')]) ){
            $nowData = $reportData;
        } else {
            $nowData = $this->getReportData($companyId, $now->format('Y'));
        }

        if(isset($nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
            $monthSearchCount = $nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
        }
        if(isset($nowData['year'][$now->format('Y')]['totalSearchCount'])){
            $yearSearchCount = $nowData['year'][$now->format('Y')]['totalSearchCount'];
        }
        if(isset($reportData['deposit'])){
            $depositList = $reportData['deposit'];
        }

        $retAry = [
            'date' => $date,
            'companyName' => $companyName,
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'webDepositInfo' => $webDepositInfo,
            'apiDepositInfo' => $apiDepositInfo,
            'depositList' => $depositList,
            'pageList' => $pageData,
            'detail' => $detail,
            'pageNo' => $pageNo,
        ];

        return $retAry;

    }

    /**
     * レポート用データ取得
     *
     * @param $companyId
     * @param $year
     * @return array|null
     * @throws Exception
     */
    private function getReportData($companyId, $year): array|null
    {
        $keywordModel = new TKeywordHistory();
        $acurisKeywordModel= new TAcurisKeywordHistory();
        $tKeywordHistoryDetail= new TKeywordHistoryDetail();
        $mUserDetailModel = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);
        //契約開始日
        $startDate = $contractPlanModel->getStartDate($companyId);

        $fromDate = new DateTime($startDate);

        $data = [];

        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);

        //レポートデータ初期化
        $data['month'] = $this->initReportData($fromDate, $year);

        $data['year'] = [];
        $data['deposit'][self::PLAN_TYPE_WEB] = [];
        $data['deposit'][self::PLAN_TYPE_API] = [];

        //月別ループ
        foreach($data['month'] as $year => $monthList){
            foreach($monthList as $key => $monthItem){

                $data['month'][$year][$key]['report'] = [];
                $data['month'][$year][$key]['totalSearchCount'] = 0;
                $data['month'][$year][$key]['totalSearchPrice'] = 0;
                $data['month'][$year][$key]['totalDupSearchCount'] = 0;

                $data['month'][$year][$key]['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, $monthItem['startDate'], $monthItem['endDate']);

                //トライアル時の検索数情報
                //WEB
                if(!is_null($webPlanInfo)){
                    $webEndTrial = date("Y-m-d",strtotime($webPlanInfo['useStartDate']."-1 day"));
                    //トライアル開始日/終了日が月初/月末を超過する場合 日付調整
                    $dateInfo = $this->adjustStartEndDate($monthItem['startDate'], $monthItem['endDate'], $webPlanInfo['startTrial'], $webEndTrial);
                    $webTrialStartDate = $dateInfo['startDate'];
                    $webTrialEndDate = $dateInfo['endDate'];

                    $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webTrialStartDate, $webTrialEndDate, true);
                    //トライアル期間の検索がある場合
                    if(!is_null($webTrialSearchList)){
                        foreach($webTrialSearchList as $searchItem){
                            $unitPrice = $webPlanInfo['trialSearchUnitPrice'];;
                            $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                            $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $webTrialStartDate, $webTrialEndDate);

                            $data['month'][$year][$key]['report'][] = [
                                'userId' => $searchItem['userId'],
                                'userName' => $searchItem['name'].' (トライアル)',
                                'unitPrice' => $unitPrice,
                                'count' => $searchItem['searchCount'],
                                'price' => $price,
                                'contractStartDate' => $webPlanInfo['startTrial'], 
                                'contractEndDate' => $webEndTrial,
                                'chargeFlg' => $searchItem['chargeFlg'],
                                'dupCount' => $dupSearchCount,
                                'type' => 'normal',
                            ];

                            //月毎検索数/金額/同一ワード検索数
                            $data['month'][$year][$key]['totalSearchCount'] += $searchItem['searchCount'];
                            $data['month'][$year][$key]['totalSearchPrice'] += $price;
                            $data['month'][$year][$key]['totalDupSearchCount'] += $dupSearchCount;
                        }
                    }
                }

                //API
                if(!is_null($apiPlanInfo)){
                    $apiEndTrial = date("Y-m-d",strtotime($apiPlanInfo['useStartDate']."-1 day"));
                    //トライアル開始日/終了日が月初/月末を超過する場合 日付調整
                    $dateInfo = $this->adjustStartEndDate($monthItem['startDate'], $monthItem['endDate'], $apiPlanInfo['startTrial'], $apiEndTrial);
                    $apiTrialStartDate = $dateInfo['startDate'];
                    $apiTrialEndDate = $dateInfo['endDate'];

                    $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiTrialStartDate, $apiTrialEndDate, true);
                    //トライアル期間の検索がある場合
                    if(!is_null($apiTrialSearchList)){
                        foreach($apiTrialSearchList as $searchItem){
                            $unitPrice = $apiPlanInfo['trialSearchUnitPrice'];
                            $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                            $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $apiTrialStartDate, $apiTrialEndDate);

                            $data['month'][$year][$key]['report'][] = [
                                'userId' => $searchItem['userId'],
                                'userName' => $searchItem['name'].' (トライアル)',
                                'unitPrice' => $unitPrice,
                                'count' => $searchItem['searchCount'],
                                'price' => $price,
                                'contractStartDate' => $apiPlanInfo['startTrial'], 
                                'contractEndDate' => $apiEndTrial,
                                'chargeFlg' => $searchItem['chargeFlg'],
                                'dupCount' => $dupSearchCount,
                                'type' => 'normal',
                            ];

                            //月毎検索数/金額/同一ワード検索数
                            $data['month'][$year][$key]['totalSearchCount'] += $searchItem['searchCount'];
                            $data['month'][$year][$key]['totalSearchPrice'] += $price;
                            $data['month'][$year][$key]['totalDupSearchCount'] += $dupSearchCount;
                        }
                    }
                }

                //プラン別ループ(tContractPlanDetail)
                foreach($data['month'][$year][$key]['contractInfo'] as $contractItem){
                    //適用開始日/終了日が月初/月末を超過する場合 日付調整
                    if($monthItem['startDate'] > $contractItem->contractStartDate){
                        $contractStartDate = $monthItem['startDate'];
                    }else{
                        $contractStartDate = $contractItem->contractStartDate;
                    }
                    if($monthItem['endDate'] < $contractItem->contractEndDate){
                        $contractEndDate = $monthItem['endDate'];
                    }else{
                        $contractEndDate = $contractItem->contractEndDate;
                    }

                    //検索数情報
                    $searchList = $keywordModel->getSearchCountByReport($companyId, $userIds[$contractItem->planType], $contractItem->planType, $contractStartDate, $contractEndDate);
                    $wkAry = [];

                    foreach($searchList as $searchItem){

                        $unitPrice = 0;
                        $price = 0;
                        $depositName = '';
                        $dupSearchCount = 0;

                        //全額デポジット かつ chargeFlg=1 はデポ料金に含める
                        if($searchItem['chargeFlg'] === 1 && $contractItem->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){

                            if(!isset($data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['unitPrice'])){
                                //単価未登録の場合、0円として表示
                                $data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['unitPrice'] = empty($contractItem->searchUnitPrice) ? 0 : $contractItem->searchUnitPrice;
                                $data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['count'] = 0;
                                $data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['price'] = 0;
                            }
                            $data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['count'] += $searchItem['searchCount'];
                            $data['deposit'][$contractItem->planType][$contractItem->searchUnitPrice]['price'] += $contractItem->searchUnitPrice * $searchItem['searchCount'];

                            continue;

                        //全額デポジット かつ chargeFlg=0 は検索料金無し
                        }elseif($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){
                            $unitPrice = 0;
                            $price = 0;
                            $depositName= ' (デポジット内)';
                        }else{
                            $unitPrice = empty($contractItem->searchUnitPrice) ? 0 : $contractItem->searchUnitPrice;
                            $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                        }
                        $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $contractStartDate, $contractEndDate);
                        
                        $wkAry[] = [
                            'userId' => $searchItem['userId'],
                            'userName' => $searchItem['name'].$depositName,
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem['searchCount'],
                            'price' => $price,
                            'contractStartDate' => $contractItem->contractStartDate,
                            'contractEndDate' => $contractItem->contractEndDate,
                            'chargeFlg' => $searchItem['chargeFlg'],
                            'dupCount' => $dupSearchCount,
                            'type' => 'normal',
                        ];
                        
                        //月毎検索数/金額/同一ワード検索数
                        $data['month'][$year][$key]['totalSearchCount'] += $searchItem['searchCount'];
                        $data['month'][$year][$key]['totalSearchPrice'] += $price;
                        $data['month'][$year][$key]['totalDupSearchCount'] += $dupSearchCount;
                    }

                    $data['month'][$year][$key]['report'] = array_merge($data['month'][$year][$key]['report'], $wkAry);
                }

                // 海外検索(Acuris)
                $acurisSearchData = $acurisKeywordModel->getMonthSearchDataByUserId($companyId, $monthItem['startDate'], $monthItem['endDate']);

                $wkAcurisAry = [];
                foreach($acurisSearchData as $searchItem){

                    // アキュリス検索(一覧)
                    $unitPrice = config('hds.acuris.search.normal.unitPrice');
                    $count = $searchItem->searchCount;
                    $price = $unitPrice * $count;

                    if($count > 0){
                        $acurisName = ' ('.config('hds.acuris.search.normal.title').')';
                        $wkAcurisAry[] = [
                            'userId' => $searchItem->userId,
                            'userName' => $mUserDetailModel->getUserName($companyId, $searchItem->userId).$acurisName,
                            'unitPrice' => $unitPrice,
                            'count' => $count,
                            'price' => $price,
                            'dupCount' => 0,
                            'type' => 'acruis',
                        ];

                        //月毎検索数/金額
                        $data['month'][$year][$key]['totalSearchCount'] += $count;
                        $data['month'][$year][$key]['totalSearchPrice'] += $price;                        
                    }

                    // アキュリス検索(詳細)
                    $unitPrice = config('hds.acuris.search.detail.unitPrice');
                    $count = $searchItem->lookupCount;
                    $price = $unitPrice * $count;

                    if($count > 0){
                        $acurisName = ' ('.config('hds.acuris.search.detail.title').')';
                        $wkAcurisAry[] = [
                            'userId' => $searchItem->userId,
                            'userName' => $mUserDetailModel->getUserName($companyId, $searchItem->userId).$acurisName,
                            'unitPrice' => $unitPrice,
                            'count' => $count,
                            'price' => $price,
                            'dupCount' => 0,
                            'type' => 'acruis',
                        ];

                        //月毎検索数/金額
                        $data['month'][$year][$key]['totalSearchCount'] += $count;
                        $data['month'][$year][$key]['totalSearchPrice'] += $price;
                    }

                }

                $data['month'][$year][$key]['report'] = array_merge($data['month'][$year][$key]['report'], $wkAcurisAry);

                // 通常検索・Acuris検索の表示順ソート
                $userIdSortAry  = array_column($data['month'][$year][$key]['report'], 'userId');
                $typeSortAry  = array_column($data['month'][$year][$key]['report'], 'type');
                if(!is_null($data['month'][$year][$key]['report'])){
                    array_multisort($userIdSortAry, SORT_ASC, $typeSortAry, SORT_DESC, $data['month'][$year][$key]['report']);
                }

                if(!isset($data['year'][substr($key,0,4)]['totalSearchCount'])){
                    $data['year'][substr($key,0,4)]['totalSearchCount'] = 0;
                    $data['year'][substr($key,0,4)]['totalSearchPrice'] = 0;
                    $data['year'][substr($key,0,4)]['totalDupSearchCount'] = 0;
                }
                
                //年毎 検索数/金額/同一ワード検索数
                $data['year'][substr($key,0,4)]['totalSearchCount'] += $data['month'][$year][$key]['totalSearchCount'];
                $data['year'][substr($key,0,4)]['totalSearchPrice'] += $data['month'][$year][$key]['totalSearchPrice'];
                $data['year'][substr($key,0,4)]['totalDupSearchCount'] += $data['month'][$year][$key]['totalDupSearchCount'];
            }
        }

        return $data;
    }

    /**
     * レポート用データ初期化
     *
     * @param $fromDate
     * @param $year
     * @return array
     */
    private function initReportData($fromDate, $year): array
    {
        $dateInfoAry = [];
        $nowMonth = new DateTime();
        $nowMonth->modify('last day of this month');
        $startDateSetFlg = true;

        //ユーザー作成日から現在まで
        while($fromDate <= $nowMonth){

            if($startDateSetFlg === true){

                //startDateを契約開始日で設定
                $dateInfoAry[$fromDate->format('Y')][$fromDate->format('Y-m')] = [
                    'startDate' => $fromDate->format('Y-m-d'),
                    'endDate' => $fromDate->format('Y-m-t'),
                ];
                $startDateSetFlg = false;
            }else{

                //startDateを月初日で設定
                $dateInfoAry[$fromDate->format('Y')][$fromDate->format('Y-m')] = [
                    'startDate' => $fromDate->format('Y-m-01'),
                    'endDate' => $fromDate->format('Y-m-t'),
                ];
            }

            $fromDate->modify('+1 months');
        }
    
        if(!isset($dateInfoAry[$year])){
            //指定年のデータが無い場合
            $retAry = [];
        }else{
            //指定年のデータのみを戻り値に設定
            $retAry[$year] = $dateInfoAry[$year];
        }

        //日付降順
        krsort($retAry);
        foreach($retAry as $year => $month){
            krsort($retAry[$year]);
        }

        return $retAry;
    }


    /**
     * 開始日/終了日を月初/月末に調整
     *
     * @param $monthStart
     * @param $monthEnd
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private function adjustStartEndDate($monthStart, $monthEnd, $startDate, $endDate): array
    {
        //開始日
        if($monthStart > $startDate){
            $dateInfo['startDate'] = $monthStart;
        }else{
            $dateInfo['startDate'] = $startDate;
        }

        //終了日
        if($monthEnd < $endDate){
            $dateInfo['endDate'] = $monthEnd;
        }else{
            $dateInfo['endDate'] = $endDate;
        }

        return $dateInfo;
    }

    /**
     * 年別ページネーションを取得
     *
     * @param $companyId
     * @param $pageNo
     * @param $loginUser
     * @return LengthAwarePaginator
     */
    private function getReportPageData($companyId, $pageNo, $loginUser): LengthAwarePaginator
    {
        $contractPlanModel = new TContractPlan();

        $startDate = $contractPlanModel->getStartDate($companyId);

        $now = new DateTime();
        $nowYear = $now->format('Y');
        $startDateTime = new DateTime($startDate);
        $startYear = $startDateTime->format('Y');

        $yearList = [];
        for($loopYear=$nowYear; $loopYear >= $startYear; $loopYear--){

            $yearList[] = (string)$loopYear;
        }

        $collection = collect($yearList);

        if($loginUser === 'admin'){
            // 管理者の場合 対象ユーザーの月別検索数を表示
            $path = array('path' => '/manage/user/searchReport/'.$companyId);
        }else{
            // 一般ユーザーの場合 自身の利用明細を表示
            $path = array('path' => '/user/useReport/');
        }

        $pageData = new LengthAwarePaginator(
            $collection->forPage($pageNo, 1),
            count($collection),
            1, // 1ページ行数
            $pageNo, // ページ番号
            $path,
        );

        return $pageData;
    }

    /**
     * ページネーション指定年を取得
     *
     * @param $pageData
     * @return string|null
     */
    private function getYearByPagination($pageData): string|null
    {

        $pageAry = $pageData->items();
        $pageItem = array_values($pageAry);

        $year = null;
        if(!empty($pageItem)){
            $year = $pageItem[0];
        }

        return $year;
    }

}
