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
        $data = [];
        $companyInfo = $userDetail->getByCompanyId($companyId);

        foreach($companyInfo as $userInfo){
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
                    'user' => $userInfo->userId.' / '.$userInfo->name,
                    'count' => $monthlySearchCount,
                ];

                //検索数を月ごとに合算
                if(array_key_exists('monthTotalCount', $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')])){
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalCount'] += $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['monthList'][$loopMonth->format('Y-m')]['monthTotalCount'] = $monthlySearchCount;
                }
                
                if(!array_key_exists('year', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['year'] = $loopMonth->format('Y');
                }

                //検索数を年ごとに合算
                if(array_key_exists('yearTotalCount', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['yearTotalCount'] += $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['yearTotalCount'] = $monthlySearchCount;
                }


                //年検索数をユーザー毎に算出
                if(!array_key_exists('userInfo', $data[$loopMonth->format('Y')])){
                    $data[$loopMonth->format('Y')]['userInfo'] = [];
                }
                if(array_key_exists($userInfo->userId, $data[$loopMonth->format('Y')]['userInfo'])){
                    $data[$loopMonth->format('Y')]['userInfo'][$userInfo->userId]['count'] += $monthlySearchCount;
                }else{
                    $data[$loopMonth->format('Y')]['userInfo'][$userInfo->userId] = [
                        'user' => $userInfo->userId.' / '.$userInfo->name,
                        'count' => $monthlySearchCount,
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


}
