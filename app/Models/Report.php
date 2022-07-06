<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;


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
     * @param $byMonthFlg
     * @param $targetMonth
     * @return array|null
     * @throws Exception
     */
    public function getReportData($companyId, $byMonthFlg = false, $targetMonth = null): array|null
    {
        $keywordModel = new TKeywordHistory();
        $mContractPlanModel = new MContractPlan();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);
        $trialUnitPrice = $mContractPlanModel->get('trial')->unitPrice;
        $startDate = $contractPlanModel->getStartDate($companyId);
       
        //月別表示で月指定されている場合
        if($byMonthFlg && !is_null($targetMonth)){
            //集計開始月を更新
            $startDate = $targetMonth;
        
        //月別表示で月指定されていない場合
        }elseif($byMonthFlg && is_null($targetMonth)){
            return null;
        }

        $fromMonth = new DateTime($startDate);

        //現在より先の日付が指定された場合(月別指定時のみ)
        if($fromMonth > new DateTime() && $byMonthFlg){
            return null;
        }        

        $data = [];
        $webTrialFlg = true;
        $apiTrialFlg = true;

        if($byMonthFlg){
            //月別表示
            $data['month'] = $this->initReportDataByMonth($fromMonth);
        }else{
            //全件表示
            $data['month'] = $this->initReportData($fromMonth);
        }

        $data['year'] = [];

        //月別ループ
        foreach($data['month'] as $key => $monthItem){

            $data['month'][$key]['report'] = [];
            $data['month'][$key]['totalSearchCount'] = 0;
            $data['month'][$key]['totalSearchPrice'] = 0;

            $data['month'][$key]['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, $monthItem['startDate'], $monthItem['endDate']);

            //プラン別ループ(tContractPlanDetail)
            foreach($data['month'][$key]['contractInfo'] as $contractItem){
                
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

                //トライアル時の検索数情報
                if($contractItem->planType === self::PLAN_TYPE_WEB && $webTrialFlg === true) {
                    $webTrialFlg = false;
                    $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $contractItem->planType, $webPlanInfo['startTrial'], $webPlanInfo['useStartDate']);
                    
                    foreach($webTrialSearchList as $searchItem){
                        //全額デポジット かつ chargeFlg=0 は検索料金無し
                        if($searchItem->chargeFlg === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                            $unitPrice = 0;
                            $price = 0;
                        }else{
                            $unitPrice = $trialUnitPrice;
                            $price = $trialUnitPrice * $searchItem->searchCount;
                        }

                        $data['month'][$key]['report'] = [
                            'userId' => $searchItem->userId,
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem->searchCount,
                            'price' => $price,
                            'contractStartDate' => $contractItem->contractStartDate,
                            'contractEndDate' => $contractItem->contractEndDate,
                            'chargeFlg' => $searchItem->chargeFlg,
                        ];
    
                    }
                }
                if($contractItem->planType === self::PLAN_TYPE_API && $apiTrialFlg === true) {
                    $apiTrialFlg = false;
                    $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $contractItem->planType, $apiPlanInfo['startTrial'], $apiPlanInfo['useStartDate']);
                
                    foreach($apiTrialSearchList as $searchItem){
                        //全額デポジット かつ chargeFlg=0 は検索料金無し
                        if($searchItem->chargeFlg === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                            $unitPrice = 0;
                            $price = 0;
                        }else{
                            $unitPrice = $trialUnitPrice;
                            $price = $trialUnitPrice * $searchItem->searchCount;
                        }

                        $data['month'][$key]['report'] = [
                            'userId' => $searchItem->userId,
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem->searchCount,
                            'price' => $price,
                            'contractStartDate' => $contractItem->contractStartDate,
                            'contractEndDate' => $contractItem->contractEndDate,
                            'chargeFlg' => $searchItem->chargeFlg,
                        ];

                    }
                }

                //検索数情報
                $searchList = $keywordModel->getSearchCountByReport($companyId, $contractItem->planType, $contractStartDate, $contractEndDate);
                $wkAry = [];

                foreach($searchList as $searchItem){
                    //全額デポジット かつ chargeFlg=0 は検索料金無し
                    if($searchItem->chargeFlg === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                        $unitPrice = 0;
                        $price = 0;
                    }else{
                        $unitPrice = $contractItem->searchUnitPrice;
                        $price = $contractItem->searchUnitPrice * $searchItem->searchCount;
                    }

                    $wkAry[] = [
                        'userId' => $searchItem->userId,
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem->searchCount,
                        'price' => $price,
                        'contractStartDate' => $contractItem->contractStartDate,
                        'contractEndDate' => $contractItem->contractEndDate,
                        'chargeFlg' => $searchItem->chargeFlg,
                    ];

                    //月毎検索数/金額
                    $data['month'][$key]['totalSearchCount'] += $searchItem->searchCount;
                    $data['month'][$key]['totalSearchPrice'] += $price;
                }

                $data['month'][$key]['report'] = array_merge($data['month'][$key]['report'], $wkAry);
            }

            if(!isset($data['year'][substr($key,0,4)]['totalSearchCount'])){
                $data['year'][substr($key,0,4)]['totalSearchCount'] = 0;
                $data['year'][substr($key,0,4)]['totalSearchPrice'] = 0;
            }
            
            //年毎検索数/金額
            $data['year'][substr($key,0,4)]['totalSearchCount'] += $data['month'][$key]['totalSearchCount'];
            $data['year'][substr($key,0,4)]['totalSearchPrice'] += $data['month'][$key]['totalSearchPrice'];
        }

        return $data;
    }

    /**
     * レポート用データ初期化
     *
     * @param $fromMonth
     * @return array
     */
    private function initReportData($fromMonth): array
    {

        $monthAry = [];
        $nowMonth = new DateTime();
        $monthFlg = true;

        //ユーザー作成日から現在まで
        while($fromMonth <= $nowMonth){

            if($monthFlg === true){

                $monthAry[$fromMonth->format('Y-m')] = [
                    'startDate' => $fromMonth->format('Y-m-d'),
                    'endDate' => $fromMonth->format('Y-m-t'),
                ];
    
                $monthFlg = false;

            }else{

                $monthAry[$fromMonth->format('Y-m')] = [
                    'startDate' => $fromMonth->format('Y-m-1'),
                    'endDate' => $fromMonth->format('Y-m-t'),
                ];
            }

            $fromMonth->modify('+1 months');
        }

        return $monthAry;
    }

    /**
     * レポート用データ初期化(月指定)
     *
     * @param $fromMonth
     * @return array
     */
    private function initReportDataByMonth($fromMonth): array
    {

        $monthAry = [];

        $monthAry[$fromMonth->format('Y-m')] = [
            'startDate' => $fromMonth->format('Y-m-1'),
            'endDate' => $fromMonth->format('Y-m-t'),
        ];

        return $monthAry;
    }
}
