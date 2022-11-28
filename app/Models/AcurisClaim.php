<?php

namespace App\Models;

use DateTime;

class AcurisClaim extends BaseModel
{

    /**
     * 請求情報を取得
     *
     * @param $claimMonth
     * @param $data
     * @return array
     */
    public function getPrice($claimMonth, $data): array
    {
        $ret = [];
        $payPerUseAry = [];
        $totalPrice = 0;

        $month = strtotime($claimMonth);
        $startDate = date("Y-m-01", $month);
        $endDate = date("Y-m-t", $month);

        $acurisKeywordModel = new TAcurisKeywordHistory();
        $searchData = $acurisKeywordModel->getSearchDataByCompanyId($data->companyId, $startDate, $endDate);

        foreach($searchData as $searchItem){

            // 一覧検索
            if($searchItem->searchCount > 0){
                $unitPrice = config('hds.acuris.search.normal.unitPrice');
                $price = $searchItem->searchCount * $unitPrice;

                $payPerUseAry[] = [
                    'amount' => $searchItem->searchCount,
                    'unitPrice' => $unitPrice,
                    'price' => $price,
                    'detailFlg' => SELF::DETAIL_FLG_OFF
                ];

                $totalPrice += $price;
            }
            
            // 詳細検索
            if($searchItem->detailSearchCount > 0){
                $unitPrice = config('hds.acuris.search.detail.unitPrice');
                $price = $searchItem->detailSearchCount * $unitPrice;

                $payPerUseAry[] = [
                    'amount' => $searchItem->detailSearchCount,
                    'unitPrice' => $unitPrice,
                    'price' => $price,
                    'detailFlg' => SELF::DETAIL_FLG_ON
                ];

                $totalPrice += $price;
            }
        }

        $payPerUseAry['total'] = $totalPrice;

        $ret = [
            'payPerUse' => $payPerUseAry,
            'totalPrice' => $totalPrice
        ];

        return $ret;
    }


    /**
     * 請求検索詳細取得
     *
     * @param $comapnyId
     * @param $claimMonth
     * @return array
     */
    public function getSearchDetail($comapnyId, $claimMonth): array
    {
        $acurisKeywordModel = new TAcurisKeywordHistory();
        $userDetailModel = new MUserDetail();

        $month = strtotime($claimMonth);
        $startDate = date("Y-m-01", $month);
        $endDate = date("Y-m-t", $month);

        $monthSearchData = $acurisKeywordModel->getMonthSearchDataByUserId($comapnyId, $startDate, $endDate);

        $acurisTotalCount = 0;
        $acurisTotalPrice = 0;
        $retAry['searchList'] = [];

        foreach($monthSearchData as $item){
            // アキュリス検索(一覧)
            $unitPrice = config('hds.acuris.search.normal.unitPrice');
            $count = $item->searchCount;
            $price = $unitPrice * $count;

            if($count > 0){
                $retAry['searchList'][] = [
                    'userId' => $item->userId,
                    'userName' => $userDetailModel->getUserName($comapnyId, $item->userId),
                    'title' => config('hds.acuris.search.normal.title'),
                    'unitPrice' => $unitPrice,
                    'count' => $count,
                    'price' => $price,
                ];
            }

            // アキュリス検索(詳細)
            $unitPrice = config('hds.acuris.search.detail.unitPrice');
            $count = $item->detailSearchCount;
            $price = $unitPrice * $count;

            if($count > 0){
                $retAry['searchList'][] = [
                    'userId' => $item->userId,
                    'userName' => $userDetailModel->getUserName($comapnyId, $item->userId),
                    'title' => config('hds.acuris.search.detail.title'),
                    'unitPrice' => $unitPrice,
                    'count' => $count,
                    'price' => $price,
                ];
                $acurisTotalCount += $count;
                $acurisTotalPrice += $price;
            }
        }

        $retAry['totalCount'] = $acurisTotalCount;
        $retAry['totalPrice'] = $acurisTotalPrice;

        return $retAry;
    }

}
