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

    const TYPE_ALL_DEPOSIT = 'allDepo';
    const TYPE_ID_DEPOSIT = 'idDepo';
    const TYPE_MONTHLY = 'allMonth';

    /**
     * レポート用データ取得
     *
     * @param $companyId
     * @param $year
     * @return array|null
     * @throws Exception
     */
    public function getReportData($companyId, $year, $month = null): array|null
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
        $data['month'] = $this->initReportData($fromDate, $year, $month);

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
                        if($searchItem['chargeFlg'] === 1 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){

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
                        }elseif($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
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
    private function initReportData($fromDate, $year, $month): array
    {
        $monthAry = [];
        $nowMonth = new DateTime();
        $nowMonth->modify('last day of this month');
        $monthFlg = true;

        //月指定の場合
        if(is_null($month) === false){

            $monthAry[$month->format('Y')][$month->format('Y-m')] = [
                'startDate' => $month->format('Y-m-01'),
                'endDate' => $month->format('Y-m-t'),
            ];

        //全体表示
        }else{
            //ユーザー作成日から現在まで
            while($fromDate <= $nowMonth){
                
                //startDateを契約開始日として設定
                if($monthFlg === true){
                    $monthAry[$fromDate->format('Y')][$fromDate->format('Y-m')] = [
                        'startDate' => $fromDate->format('Y-m-d'),
                        'endDate' => $fromDate->format('Y-m-t'),
                    ];
                    
                    $monthFlg = false;
                    
                    //startDateを月初として設定
                }else{
                    
                    $monthAry[$fromDate->format('Y')][$fromDate->format('Y-m')] = [
                        'startDate' => $fromDate->format('Y-m-01'),
                        'endDate' => $fromDate->format('Y-m-t'),
                    ];
                }
                
                $fromDate->modify('+1 months');
            }
        }
        
        //ページネート指定年データが無い場合
        if(!isset($monthAry[$year])){
            $retAry = [];
        }else{
            $retAry[$year] = $monthAry[$year];
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
     * @return array
     */
    public function getReportPageInfo($companyId, $pageNo, $type = 'user'): array
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

        if($type === 'manage'){
            $path = array('path' => '/manage/user/searchReport/'.$companyId);

        }else{
            $path = array('path' => '/user/useReport/');
        }

        $pageData = new LengthAwarePaginator(
            $collection->forPage($pageNo, 1),
            count($collection),
            1, // 1ページ行数
            $pageNo, // ページ番号
            $path,
        );

        $retAry=[
            'pageData' => $pageData,
            'yearList' => $yearList
        ];

        return $retAry;
    }

}
