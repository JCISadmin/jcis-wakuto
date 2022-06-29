<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;
use TCPDF;


/**
 * 検索
 */
class Report extends BaseModel
{
    use HasFactory;


    /**
     * レポート用データ取得
     *
     * @param $companyId
     * @param $fileName
     * @return array
     * @throws Exception
     */
    public function getReportData($companyId): array
    {
        $keywordModel = new TKeywordHistory();
        $userDetail = new MUserDetail();
        $userCompany = new MUserCompany();
        $contractPlanModel = new TContractPlan();
        $data = [];
        $companyInfo = $userDetail->getByCompanyId($companyId);

        foreach($companyInfo as $userInfo){

            //検索単価情報を取得
            $contractData = $contractPlanModel->getPlanUsePlanId($userInfo->companyId, $userInfo->contractPlanId);

            //月別検索数を取得
            $searchCountData = $keywordModel->getSearchCountByMonth($companyId, $userInfo->userId);
            $createMonth = $userCompany->getCreateMonth($companyId);

            $nowMonth = new DateTime();
            $nextMonth = $nowMonth->modify('+1 months');
            $loopMonth = new DateTime($createMonth);
            //ユーザー作成日から現在まで
            while($loopMonth->format('Y-m') !== $nextMonth->format('Y-m')){

                $monthlySearchCount = 0;
                //月検索数取得
                foreach($searchCountData as $monthlySearchCountData){
                    if($monthlySearchCountData->searchMonth === $loopMonth->format('Y-m') ){
                        $monthlySearchCount =  $monthlySearchCountData->MonthlySearchCount;
                        break;
                    }
                }

                $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['month'] = $loopMonth->format('Y-m');
                $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['userInfo'][$userInfo->userId] = [
                    'unitPrice' => $contractData->searchUnitPrice,
                    'user' => $userInfo->userId.' / '.$userInfo->name,
                    'count' => $monthlySearchCount,
                    'price' => $contractData->searchUnitPrice * $monthlySearchCount,
                ];

                //検索数・金額を月ごとに合算
                if(array_key_exists('monthTotalCount', $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')])){
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalCount'] += $monthlySearchCount;
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalPrice'] += $contractData->searchUnitPrice * $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalCount'] = $monthlySearchCount;
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalPrice'] = $contractData->searchUnitPrice * $monthlySearchCount;
                }
                
                if(!array_key_exists('year', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['year'] = $loopMonth->format('Y');
                }

                //検索数・金額を年ごとに合算
                if(array_key_exists('yearTotalCount', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['yearTotalCount'] += $monthlySearchCount;
                    $data[$loopMonth->format('Y')]['yearTotalPrice'] += $contractData->searchUnitPrice * $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['yearTotalCount'] = $monthlySearchCount;
                    $data[$loopMonth->format('Y')]['yearTotalPrice'] = $contractData->searchUnitPrice * $monthlySearchCount;
                }


                //年検索数・年金額をユーザー毎に算出
                if(!array_key_exists('userInfo', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['userInfo'] = [];
                }
                if(array_key_exists($userInfo->userId, $data[$loopMonth->format('Y')]['userInfo'])){
                    $data[$loopMonth->format('Y')]['userInfo'][$userInfo->userId]['count'] += $monthlySearchCount;
                    $data[$loopMonth->format('Y')]['userInfo'][$userInfo->userId]['price'] += $contractData->searchUnitPrice * $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['userInfo'][$userInfo->userId] = [
                        'user' => $userInfo->userId.' / '.$userInfo->name,
                        'count' => $monthlySearchCount,
                        'price' => $contractData->searchUnitPrice * $monthlySearchCount,
                    ];
                }

                $loopMonth = $loopMonth->modify('+1 months');
            }
        }

        krsort($data);
        foreach($data as $year => $item){
            krsort($data[$year]['monthList']);
        }

        return $data;
    }


    /**
     * レポート用データ取得(月指定)
     *
     * @param $companyIdp
     * @param $fileName
     * @return array
     * @throws Exception
     */
    public function getReportDatabyMonth($companyId,$trgtMonth): array
    {
        $keywordModel = new TKeywordHistory();
        $userDetail = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $data = [];
        $companyInfo = $userDetail->getByCompanyId($companyId);
        $date = new Datetime($trgtMonth);
        $startDate = date_format($date, 'Y-m-d 0:00:00');
        $endDate = date_format($date->modify('+01 month -01 day'), 'Y-m-d 23:59:59');

        foreach($companyInfo as $userInfo){
            
            //検索単価情報を取得
            $contractData = $contractPlanModel->getPlanUsePlanId($userInfo->companyId, $userInfo->contractPlanId);

            $searchCountData = $keywordModel->getSearchCount($companyId, $userInfo->contractPlanId, $userInfo->userId, $startDate, $endDate);
            $data[$date->format('Y')]['monthList'][$trgtMonth]['month'] = $trgtMonth;
            $data[$date->format('Y')]['monthList'][$trgtMonth]['userInfo'][$userInfo->userId] = [
                'unitPrice' => $contractData->searchUnitPrice,
                'user' => $userInfo->userId.' / '.$userInfo->name,
                'count' => $searchCountData,
                'price' => $contractData->searchUnitPrice * $searchCountData,
            ];
    
            //検索数・金額を月ごとに合算
            if(array_key_exists('monthTotalCount', $data[$date->format('Y')]['monthList'][$trgtMonth])){
                $data[$date->format('Y')]['monthList'][$trgtMonth]['monthTotalCount'] += $searchCountData;
                $data[$date->format('Y')]['monthList'][$trgtMonth]['monthTotalPrice'] += $contractData->searchUnitPrice * $searchCountData;
            }else{
                $data[$date->format('Y')]['monthList'][$trgtMonth]['monthTotalCount'] = $searchCountData;
                $data[$date->format('Y')]['monthList'][$trgtMonth]['monthTotalPrice'] = $contractData->searchUnitPrice * $searchCountData;
            }
        }
    
        krsort($data);

        return $data;
    }
}
