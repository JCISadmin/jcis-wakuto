<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

/**
 * ユーザーマスタ
 */
class UsageStatus extends Report
{
    use HasFactory;

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';

    /**
     * ユーザー一覧の取得
     *
     * @param $companyName
     * @param $contractStatus
     * @param $contractPlan
     * @param $useEndAlertDate
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($companyName, $contractStatus, $contractPlan, $useEndAlertDate, $pageLine, $chargeName, $dispType, $startDate, $endDate): LengthAwarePaginator
    {
        //ID数
        $idNum = DB::table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        $idNum->groupBy(['companyId', 'contractPlanId']);

        //検索数
        $searchCnt = DB::table('tKeywordHistory');
        $searchCnt->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as searchCount'),
            'searchDate',
        );
        if($startDate != '' && $endDate != ''){
            $searchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $searchCnt->groupBy(['companyId', 'contractPlanId','searchDate']);

        //検索単価
        $searchInfo = DB::table('tContractPlanDetail');
        $searchInfo->select(
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.contractPlanId',
            DB::raw('sum(searchCnt.searchCount) as totalCount'),
            DB::raw('group_concat(tContractPlanDetail.searchUnitPrice) as unitPriceAry'),
            DB::raw('group_concat(searchCnt.searchCount) as countAry'),
        );
        $searchInfo->joinSub($searchCnt, 'searchCnt', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'searchCnt.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'searchCnt.contractPlanId');
            $join->on('tContractPlanDetail.contractStartDate', '<', 'searchCnt.searchDate');
            $join->on('tContractPlanDetail.contractEndDate', '>', 'searchCnt.searchDate');
        });
        $searchInfo->groupBy([
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.contractPlanId',

        ]);

        //WEB
        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'webPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry',
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->joinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->leftJoinSub($searchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'searchInfo.contractPlanId');
        });
        $webPlan->where('mContractPlan.planType', 'web');


        //API
        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'apiPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry',
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->joinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->leftJoinSub($searchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'searchInfo.contractPlanId');
        });
        $apiPlan->where('mContractPlan.planType', 'api');

        $user = DB::table('mUserCompany');

        $user->select(
            'mUserCompany.*',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.name as webPlanName',
            'webPlan.useEndAlertDate as webPlanUseEndAlertDate',
            'webPlan.useEndDate as webPlanUseEndDate',
            'webPlan.ids as webPlanIds',
            DB::raw('IFNULL(webPlan.totalCount, 0) as webPlanTotalCount'),
            'webPlan.unitPriceAry as webPlanUnitPriceAry',
            'webPlan.countAry as webPlanCountAry',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.name as apiPlanName',
            'apiPlan.useEndAlertDate as apiPlanUseEndAlertDate',
            'apiPlan.useEndDate as apiPlanUseEndDate',
            'apiPlan.ids as apiPlanIds',
            DB::raw('IFNULL(apiPlan.totalCount, 0) as apiPlanTotalCount'),
            'apiPlan.unitPriceAry as apiPlanUnitPriceAry',
            'apiPlan.countAry as apiPlanCountAry',
            DB::raw('IFNULL(webPlan.totalCount, 0) + IFNULL(apiPlan.totalCount, 0) as sumCount'),
            'mContractStatus.name as statusName',
        );

        $user->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });

        $user->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });

        $user->leftJoin('mContractStatus', function($join){
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        /* @var string $user */
        $query = DB::table($user);
        $query->where('delFlg', self::DEL_FLG_OFF);

        if ($companyName != '') {
            $query->where('name', 'like', '%' . $companyName . '%');
        }

        if ($contractStatus != '') {
            $query->where('contractStatus', $contractStatus);
        }

        if ($contractPlan != '') {
            $query->whereRaw('(webPlanPlanId = ? or apiPlanPlanId = ?)', [$contractPlan, $contractPlan]);
        }

        if ($useEndAlertDate != '') {
            $query->whereRaw('(webPlanUseEndAlertDate = ? or apiPlanUseEndAlertDate = ?)', [$useEndAlertDate, $useEndAlertDate]);
        }

        if ($chargeName != '') {
            $query->where('chargeName', $chargeName);
        }    

        if($dispType == 1){
            //検索件数 昇順
            $query->orderBy('sumCount');
        }else{
            //検索件数 降順
            $query->orderByDesc('sumCount');
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        $retAry = $query->paginate($pageLine);

        $sumSearchCount = 0;
        $sumPrice = 0;

        foreach($retAry as $item){
            $item->webPlanUnitPriceAry = explode(",", $item->webPlanUnitPriceAry);
            $item->webPlanCountAry = explode(",", $item->webPlanCountAry);
            $item->apiPlanUnitPriceAry = explode(",", $item->apiPlanUnitPriceAry);
            $item->apiPlanCountAry = explode(",", $item->apiPlanCountAry);

            $webTotalPrice = 0;
            $apiTotalPrice = 0;
            
            //合計金額(会社・単価別)
            foreach($item->webPlanCountAry as $idx => $count){
                $item->webPriceAry[$idx] = null;

                if($count == ''){
                    continue;
                }

                $item->webPriceAry[$idx] = $count * $item->webPlanUnitPriceAry[$idx];
                $webTotalPrice += $item->webPriceAry[$idx];
            }
            foreach($item->apiPlanCountAry as $idx => $count){
                $item->apiPriceAry[$idx] = null;

                if($count == ''){
                    continue;
                }

                $item->apiPriceAry[$idx] = $count * $item->apiPlanUnitPriceAry[$idx];
                $apiTotalPrice += $item->apiPriceAry[$idx];
            }

            $item->webTotalPrice = $webTotalPrice;
            $item->apiTotalPrice = $apiTotalPrice;

            $sumSearchCount += $item->webPlanTotalCount;
            $sumSearchCount += $item->apiPlanTotalCount;
            $sumPrice += $item->webTotalPrice;
            $sumPrice += $item->apiTotalPrice;
        }

        //指定期間内合計検索数・金額
        $retAry->sumSearchCount = $sumSearchCount;
        $retAry->sumPrice = $sumPrice;

        return $retAry;

    }


    /**
     * ユーザー詳細の取得
     *
     * @param $companyId
     * @return array
     */
    public function get($companyId, $seqNo = ''): array
    {
        $model = new TContractPlan();
        $contractDetail = new TContractPlanDetail();
        $data = [];

        $query = DB::table($this->table);
        $query->select(
            'mContractStatus.contractStatus',
            'mContractStatus.name as contractStatusName',
            'mUserCompany.chargeName',
            'mUserCompany.chargeMail',
            'mUserCompany.name',
            'mUserCompany.kana',
            'mUserCompany.companyId',
            'mUserCompany.postCode',
            'mUserCompany.address',
            'mUserCompany.tel',
            'mUserCompany.staffName',
            'mUserCompany.staffDepartmentJob',
            'mUserCompany.staffTel',
            'mUserCompany.staffMail',
            'mUserCompany.claimName',
            'mUserCompany.claimDepartmentJob',
            'mUserCompany.claimTel',
            'mUserCompany.claimMailTo',
            'mUserCompany.claimMailCc',
		);

        $query->join('mContractStatus', function ($join) {
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        $query->where('mUserCompany.companyId', $companyId);
        $userCompany =  (array)$query->first();

        $data['userCompany'] = $userCompany;

        $data['contractPlan']['web'] = $model->getPlan($companyId, self::TYPE_WEB, $seqNo);
        $data['contractPlan']['api'] = $model->getPlan($companyId, self::TYPE_API, $seqNo);
        if($seqNo === ''){
            $data['contractPlan']['seqNo'] = $contractDetail->getMaxSeqNo($companyId);
        }else{
            $data['contractPlan']['seqNo'] = $seqNo;
        }

        return($data);
    }


    /**
     * レポート用データ取得
     *
     * @param $companyId
     * @param $byMonthFlg
     * @param $targetMonth
     * @return array|null
     * @throws Exception
     */
    public function getReportDataByPeriod($companyId, $startDate, $endDate): array|null
    {
        $keywordModel = new TKeywordHistory();
        $tKeywordHistoryDetail= new TKeywordHistoryDetail();
        $mUserDetailModel = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);

        $data = [];
        $webTrialFlg = true;
        $apiTrialFlg = true;
        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);


        $data['report'] = [];
        $data['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, $startDate, $endDate);

        //プラン別ループ(tContractPlanDetail)
        foreach($data['contractInfo'] as $contractItem){
            //適用開始日/終了日が月初/月末を超過する場合 日付調整
            if($startDate > $contractItem->contractStartDate){
                $contractStartDate = $startDate;
            }else{
                $contractStartDate = $contractItem->contractStartDate;
            }
            if($endDate < $contractItem->contractEndDate){
                $contractEndDate = $endDate;
            }else{
                $contractEndDate = $contractItem->contractEndDate;
            }

            //トライアル時の検索数情報
            if($contractItem->planType === self::PLAN_TYPE_WEB && $webTrialFlg === true) {
                $webTrialFlg = false;
                $webEndTrial = date("Y-m-d",strtotime($webPlanInfo['useStartDate']."-1 day"));
                $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webPlanInfo['startTrial'], $webEndTrial, true);

                //トライアル期間の検索がある場合
                if(!is_null($webTrialSearchList)){

                    foreach($webTrialSearchList as $searchItem){
                        //全額デポジット かつ chargeFlg=0 は検索料金無し
                        if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                            $unitPrice = 0;
                            $price = 0;
                        }else{
                            $unitPrice = $webPlanInfo['trialSearchUnitPrice'];;
                            $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                        }

                        $data['report'][] = [
                            'user' => $searchItem['userId'].' / '.$searchItem['name'].' (トライアル)',
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem['searchCount'],
                            'price' => $price,
                            'contractStartDate' => $contractItem->contractStartDate,
                            'contractEndDate' => $contractItem->contractEndDate,
                            'chargeFlg' => $searchItem['chargeFlg'],
                        ];
        
                    }
                }
            }
            if($contractItem->planType === self::PLAN_TYPE_API && $apiTrialFlg === true) {
                $apiTrialFlg = false;
                $apiEndTrial = date("Y-m-d",strtotime($apiPlanInfo['useStartDate']."-1 day"));
                $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiPlanInfo['startTrial'], $apiEndTrial, true);
            
                //トライアル期間の検索がある場合
                if(!is_null($apiTrialSearchList)){

                    foreach($apiTrialSearchList as $searchItem){
                        //全額デポジット かつ chargeFlg=0 は検索料金無し
                        if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                            $unitPrice = 0;
                            $price = 0;
                        }else{
                            $unitPrice = $apiPlanInfo['trialSearchUnitPrice'];
                            $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                        }

                        $data['report'][] = [
                            'user' => $searchItem['userId'].' / '.$searchItem['name'].' (トライアル)',
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem['searchCount'],
                            'price' => $price,
                            'contractStartDate' => $contractItem->contractStartDate,
                            'contractEndDate' => $contractItem->contractEndDate,
                            'chargeFlg' => $searchItem['chargeFlg'],
                        ];
                    }
                }
            }

            //検索数情報
            $searchList = $keywordModel->getSearchCountByReport($companyId, $userIds[$contractItem->planType], $contractItem->planType, $contractStartDate, $contractEndDate);
            $wkAry = [];

            foreach($searchList as $searchItem){

                $depositName = '';

                //全額デポジット かつ chargeFlg=0 は検索料金無し
                if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                    $unitPrice = 0;
                    $price = 0;
                    $depositName= ' (デポジット内)';
                }else{
                    $unitPrice = $contractItem->searchUnitPrice;
                    $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                }

                $wkAry[] = [
                    'user' => $searchItem['userId'].' / '.$searchItem['name'].$depositName,
                    'unitPrice' => $unitPrice,
                    'count' => $searchItem['searchCount'],
                    'price' => $price,
                    'contractStartDate' => $contractItem->contractStartDate,
                    'contractEndDate' => $contractItem->contractEndDate,
                    'chargeFlg' => $searchItem['chargeFlg'],
                ];
            
            }
            
            $data['report'] = array_merge($data['report'], $wkAry);
        }

        return $data;
    }

}