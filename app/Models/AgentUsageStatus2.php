<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use TCPDF;

/**
 * 利用状況
 */
class AgentUsageStatus2 extends BaseModel
{
    use HasFactory;

    private string $agentDBConnection;

    /**
     * ユーザー一覧の取得
     *
     * @param $pageLine
     * @param $contractPlan
     * @param $chargeName
     * @param $dispType
     * @param $startDate
     * @param $endDate
     * @param bool $paginateFlg
     * @param int $agentNo
     * @param agent_cd 代理店番号
     * @return LengthAwarePaginator|Collection
     */
    public function getList($pageLine, $contractPlan, $chargeName, $dispType, $startDate, $endDate, bool $paginateFlg, $agent_cd, $agentNo = null): LengthAwarePaginator|Collection
    {
        $this->agentDBConnection = $this->getAgentDBConnection($agentNo);

        //ID数 クエリ
        $endMonth = date('Ym', strtotime($endDate));
        $connection = DB::connection($this->agentDBConnection);
        // $connection = DB::connection('mysql');

        $idNum = $connection->table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            $connection->raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        // $idNum->where(function ($query) use ($endMonth) {
        //     $query->where('delFlg', 0)
        //         ->orWhere(function ($query) use ($endMonth) {
        //             $query->where('delMonth', '>=', $endMonth);
        //         });
        // });
        $idNum->groupBy(['companyId', 'contractPlanId']);

        // 検索数取得クエリを生成
        $webSearchCnt = $this->buildQuerySearchCnt(self::PLAN_TYPE_WEB, $startDate, $endDate);
        $apiSearchCnt = $this->buildQuerySearchCnt(self::PLAN_TYPE_API, $startDate, $endDate);

        // 検索情報取得クエリを生成
        $webSearchInfo = $this->buildQuerySearchInfo(self::PLAN_TYPE_WEB, $webSearchCnt);
        $apiSearchInfo = $this->buildQuerySearchInfo(self::PLAN_TYPE_API, $apiSearchCnt);

        // 同一ワード検索数 クエリ
        $dupSearchCnt = $connection->table('tKeywordHistoryDetail');
        $dupSearchCnt->select(
            'companyId',
            $connection->raw('sum(searchCount) as dupSearchCount'),
        );
        if($startDate != '' && $endDate != ''){
            $dupSearchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $dupSearchCnt->groupBy([
            'companyId',
        ]);

        // WEBプラン クエリ
        $connection = DB::connection($this->agentDBConnection);
        $webPlan = $connection->table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'webPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry as countAry',
        );
        $webPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->leftJoinSub($idNum, 'webPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->leftJoinSub($webSearchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'searchInfo.planType');
        });
        $webPlan->where('mContractPlan.planType', self::PLAN_TYPE_WEB);

        // APIプラン クエリ
        $connection = DB::connection($this->agentDBConnection);
        $apiPlan = $connection->table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'apiPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry as countAry',
        );
        $apiPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->leftJoinSub($idNum, 'apiPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        
        $apiPlan->leftJoinSub($apiSearchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'searchInfo.planType');
        });
        $apiPlan->where('mContractPlan.planType', self::PLAN_TYPE_API);

        // Acurisプラン クエリ
        $connection = DB::connection($this->agentDBConnection);
        $acurisSearchCnt = $connection->table('tAcurisKeywordHistory');
        $acurisSearchCnt->select(
            'companyId',
            $connection->raw('SUM(searchCount) as searchCount'),
            $connection->raw('SUM(lookupCount) as lookupCount'),
        );
        if ($startDate != '' && $endDate != '') {
            $acurisSearchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $acurisSearchCnt->groupBy([
            'companyId',
        ]);

        // ユーザー一覧
        $connection = DB::connection($this->agentDBConnection);
        $user = $connection->table('mUserCompany');
        $user->select(
            'mUserCompany.*',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.name as webPlanName',
            'webPlan.useEndAlertDate as webPlanUseEndAlertDate',
            'webPlan.useEndDate as webPlanUseEndDate',
            'webPlan.ids as webPlanIds',
            $connection->raw('IFNULL(webPlan.totalCount, 0) as webPlanTotalCount'),
            'webPlan.unitPriceAry as webPlanUnitPriceAry',
            'webPlan.countAry as webPlanCountAry',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.name as apiPlanName',
            'apiPlan.useEndAlertDate as apiPlanUseEndAlertDate',
            'apiPlan.useEndDate as apiPlanUseEndDate',
            'apiPlan.ids as apiPlanIds',
            $connection->raw('IFNULL(apiPlan.totalCount, 0) as apiPlanTotalCount'),
            'apiPlan.unitPriceAry as apiPlanUnitPriceAry',
            'apiPlan.countAry as apiPlanCountAry',
            $connection->raw('IFNULL(acurisSearchCnt.searchCount, 0) as acurisTotalCount'),
            $connection->raw('IFNULL(acurisSearchCnt.lookupCount, 0) as acurisDetailTotalCount'),
            $connection->raw('IFNULL(webPlan.totalCount, 0) + IFNULL(apiPlan.totalCount, 0) + IFNULL(acurisSearchCnt.searchCount, 0) + IFNULL(acurisSearchCnt.lookupCount, 0) as sumCount'),
            'mContractStatus.name as statusName',
            $connection->raw('IFNULL(dupSearchCnt.dupSearchCount, 0) as dupSearchCount'),
        );

        $user->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });

        $user->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });

        $user->leftJoinSub($dupSearchCnt, 'dupSearchCnt', function($join){
            $join->on('mUserCompany.companyId', '=', 'dupSearchCnt.companyId');
        });

        $user->leftJoinSub($acurisSearchCnt, 'acurisSearchCnt', function($join){
            $join->on('mUserCompany.companyId', '=', 'acurisSearchCnt.companyId');
        });

        $user->leftJoin('mContractStatus', function($join){
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        /* @var string $user */
        $connection = DB::connection($this->agentDBConnection);
        $query = $connection->table($user);
        $query->where('delFlg', self::DEL_FLG_OFF);
        $query->where('agent_cd', $agent_cd);

        if ($contractPlan != '') {
            $query->whereRaw('(webPlanPlanId = ? or apiPlanPlanId = ?)', [$contractPlan, $contractPlan]);
        }

        if ($chargeName != '') {
            $query->where('chargeName', 'like', '%' . $chargeName . '%');
        }

        if($dispType == 1){
            //検索件数 昇順
            $query->orderBy('sumCount');
        }else{
            //検索件数 降順
            $query->orderByDesc('sumCount');
        }

        if($paginateFlg === true){
            if ($pageLine == '') {
                $pageLine = self::PAGE_LINE;
            }
            $calcAry = $query->get();
            $retAry = $query->paginate($pageLine);        
        }else{
            $calcAry = $query->get();
            $retAry = $query->get();        
        }


        //各ユーザー毎合計計算
        $calcList = $this->calcUserListData($calcAry);
        $retList = $this->calcUserListData($retAry);
        $retData = $retList['userList'];

        //指定期間内集計一覧
        // $retData->totalCom = $calcList['totalCom'];
        // $retData->sumSearchCount = $calcList['sumSearchCount'];
        // $retData->sumId = $calcList['sumId'];
        $retData->contractCom = $calcList['contractCom'];
        $retData->contractEndCom = $calcList['contractEndCom'];
        $retData->sumSearchCount = $calcList['sumSearchCount'];
        $retData->sumId = $calcList['sumId'];
        $retData->sumPrice = $calcList['sumPrice'];
        $retData->sumPriceWithTax = $calcList['sumPriceWithTax'];

        // 会社IDを暗号化
        // foreach ($retData as $idx => $item) {
        //     $retData[$idx]->companyId = Crypt::encrypt($item->companyId);
        // }

        return $retData;
    }

    /**
     * ユーザー毎検索数/料金計算
     *
     * @param $userList
     * @return array
     */
    private function calcUserListData($userList): array
    {
        $vatModel = new MVat();

        $retAry= [];
        $retAry['contractCom'] = 0;
        $retAry['contractEndCom'] = 0;
        $retAry['sumSearchCount'] = 0;
        $retAry['sumId'] = 0;
        $retAry['sumPrice'] = 0;
        $retAry['sumPriceWithTax'] = 0;

        foreach ($userList as $item) {
            $item->webPlanUnitPriceAry = explode(",", $item->webPlanUnitPriceAry);
            $item->webPlanCountAry = explode(",", $item->webPlanCountAry);
            $item->apiPlanUnitPriceAry = explode(",", $item->apiPlanUnitPriceAry);
            $item->apiPlanCountAry = explode(",", $item->apiPlanCountAry);

            $webTotalPrice = 0;
            $apiTotalPrice = 0;

            //合計金額(会社・単価別)
            foreach ($item->webPlanCountAry as $idx => $count) {

                if ($count == '') {
                    continue;
                }

                //単価が存在し無い場合スキップ
                if (!isset($item->webPlanUnitPriceAry[$idx])) {
                    continue;
                }
                $webTotalPrice += (int)$count * (int)$item->webPlanUnitPriceAry[$idx];
            }

            foreach($item->apiPlanCountAry as $idx => $count){

                if ($count == '') {
                    continue;
                }

                //単価が存在し無い場合スキップ
                if (!isset($item->apiPlanUnitPriceAry[$idx])) {
                    continue;
                }
                $apiTotalPrice += (int)$count * (int)$item->apiPlanUnitPriceAry[$idx];
            }

            $acurisNormalUnitPrice = (int)config('hds.acuris.search.normal.unitPrice');
            $acurisDetailUnitPrice = (int)config('hds.acuris.search.detail.unitPrice');
            $acurisTotalPrice = (int)$item->acurisTotalCount * $acurisNormalUnitPrice;
            $acurisDetailTotalPrice = (int)$item->acurisDetailTotalCount * $acurisDetailUnitPrice;

            $item->webTotalPrice = $webTotalPrice;
            $item->apiTotalPrice = $apiTotalPrice;
            $item->acurisTotalPrice = $acurisTotalPrice;
            $item->acurisDetailTotalPrice = $acurisDetailTotalPrice;
            
            $retAry['sumSearchCount'] += (int)$item->webPlanTotalCount;
            $retAry['sumSearchCount'] += (int)$item->apiPlanTotalCount;
            $retAry['sumSearchCount'] += (int)$item->acurisTotalCount;
            $retAry['sumSearchCount'] += (int)$item->acurisDetailTotalCount;
            $retAry['sumPrice'] += (int)$item->webTotalPrice;
            $retAry['sumPrice'] += (int)$item->apiTotalPrice;
            $retAry['sumPrice'] += (int)$item->acurisTotalPrice;
            $retAry['sumPrice'] += (int)$item->acurisDetailTotalPrice;

            $webIds = is_null($item->webPlanIds) ? 0 : $item->webPlanIds;
            $apiIds = is_null($item->apiPlanIds) ? 0 : $item->apiPlanIds;
            $retAry['sumId'] += $webIds + $apiIds;

            //契約中/トライアル中/契約終了
            switch($item->contractStatus){
                case 2:
                    $retAry['contractCom']++;
                    break;
                case 3:
                    $retAry['contractEndCom']++;
                    break;
            }
        }

        //金額(税込)
        $now = new DateTime();
        $tax = $vatModel->getTax($now);
        $taxPrice = round(($retAry['sumPrice']) * $tax / 100);
        $retAry['sumPriceWithTax'] = $retAry['sumPrice'] + $taxPrice;

        $retAry['userList'] = $userList;

        return $retAry;
    }

    /**
     * 詳細データ取得
     *
     * @param $companyId
     * @param $targetmonth
     * @param int $agentNo
     * @return array
     */
    public function getDetailData($companyId, $targetmonth, $agentNo = null): array
    {
        $contractPlanModel       = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();
        $keywordModel            = new TKeywordHistory();
        $tKeywordHistoryDetail   = new TKeywordHistoryDetail();
        $acurisKeywordModel      = new TAcurisKeywordHistory();
        $mUserDetailModel        = new MUserDetail();

        $this->agentDBConnection = $this->getAgentDBConnection($agentNo);
        $connection = DB::connection($this->agentDBConnection);

        $webPlanInfo = $contractPlanModel->getAgentPlan($connection, $companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getAgentPlan($connection, $companyId, self::PLAN_TYPE_API);

        $retAry = [];

        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getAgentList($connection, $companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getAgentList($connection, $companyId, self::PLAN_TYPE_API);

        $totalSearchCount= 0;
        $totalPrice= 0;
        $totalDupSearchCount = 0;
        $retAry['report'] = [];
        //利用状況詳細は現在までのすべての検索情報を取得
        $retAry['contractInfo'] = $contractPlanDetailModel->getAgentDetailByMonth($connection, $companyId, null, null);

        //プラン別ループ(tContractPlanDetail)
        foreach($retAry['contractInfo'] as $contractItem){
            
            $contractStartDate = $contractItem->contractStartDate;
            $contractEndDate = $contractItem->contractEndDate;

            //検索数情報
            $searchList = $keywordModel->getAgentSearchCountByReport($connection, $companyId, $userIds[$contractItem->planType], $contractItem->planType, $contractStartDate, $contractEndDate);
            $wkAry = [];

            foreach($searchList as $searchItem){

                $depositName = '';

                //全額デポジット かつ chargeFlg=0 は検索料金無し
                if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){
                    $unitPrice = 0;
                    $price = 0;
                    $depositName= ' (デポジット内)';
                }else{
                    if($searchItem['chargeFlg'] === 1) {
                        $depositName= ' (課金検索)';
                    }
                    $unitPrice = empty($contractItem->searchUnitPrice) ? 0 : $contractItem->searchUnitPrice;
                    $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                }
                $dupSearchCount = $tKeywordHistoryDetail->getAgentSearchCount($connection, $companyId, $searchItem['userId'], $contractStartDate, $contractEndDate);

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

                $totalSearchCount += $searchItem['searchCount'];
                $totalPrice += $price;
                $totalDupSearchCount += $dupSearchCount;
            }

            $retAry['report'] = array_merge($retAry['report'], $wkAry);
        }

        // 海外検索(Acuris)
        // 利用状況詳細では、全期間の検索履歴を取得
        $acurisSearchData = $acurisKeywordModel->getAgentSearchDataByUserId($connection, $companyId, NULL, NULL);

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
                    'userName' => $mUserDetailModel->getAgentUserName($connection, $companyId, $searchItem->userId).$acurisName,
                    'unitPrice' => $unitPrice,
                    'count' => $count,
                    'price' => $price,
                    'dupCount' => 0,
                    'type' => 'acuris',
                ];
        
                $totalSearchCount += $count;
                $totalPrice += $price;
            }
        
            // アキュリス検索(詳細)
            $unitPrice = config('hds.acuris.search.detail.unitPrice');
            $count = $searchItem->lookupCount;
            $price = $unitPrice * $count;
        
            if($count > 0){
                $acurisName = ' ('.config('hds.acuris.search.detail.title').')';
                $wkAcurisAry[] = [
                    'userId' => $searchItem->userId,
                    'userName' => $mUserDetailModel->getAgentUserName($connection, $companyId, $searchItem->userId).$acurisName,
                    'unitPrice' => $unitPrice,
                    'count' => $count,
                    'price' => $price,
                    'dupCount' => 0,
                    'type' => 'acuris',
                ];
        
                $totalSearchCount += $count;
                $totalPrice += $price;
            }
        }

        $retAry['report'] = array_merge($retAry['report'], $wkAcurisAry);

        // 通常検索・Acuris検索の表示順ソート
        $userIdSortAry  = array_column($retAry['report'], 'userId');
        $typeSortAry  = array_column($retAry['report'], 'type');
        if(!is_null($retAry['report'])){
            array_multisort($userIdSortAry, SORT_ASC, $typeSortAry, SORT_DESC, $retAry['report']);
        }

        $retAry['totalSearchCount'] = $totalSearchCount;
        $retAry['totalPrice'] = $totalPrice;
        $retAry['totalDupSearchCount'] = $totalDupSearchCount;

        return $retAry;
    }

    /**
     * 検索数取得クエリを生成
     * @param $type
     * @param $startDate
     * @param $endDate
     * @return
     */
    private function buildQuerySearchCnt($type, $startDate, $endDate)
    {
        $connection = DB::connection($this->agentDBConnection);
        $searchCnt = $connection->table('tKeywordHistory');
        $searchCnt->select(
            'companyId',
            'mContractPlan.planType as planType',
            $connection->raw('1 as searchCount'),
            'searchDate',
            'chargeFlg',
        );
        $searchCnt->leftJoin('mContractPlan', function ($join){
            $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $searchCnt->where('planType', $type);
        if($startDate != '' && $endDate != ''){
            $searchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }

        return $searchCnt;
    }

    /**
     * 検索情報取得クエリを生成
     * @param $type
     * @param $searchCnt
     * @return 
     */
    private function buildQuerySearchInfo($type, $searchCnt)
    {
        $connection = DB::connection($this->agentDBConnection);
        $searchInfo = $connection->table('tContractPlanDetail');

        $searchInfo->select(
            'tContractPlanDetail.companyId',
            'mContractPlan.planType as planType',
            $connection->raw('sum(searchCnt.searchCount) as totalCount'),
            //chargeFlg = 0 かつ 全額デポジット は検索単価0で集計
            $connection->raw('group_concat(IF(searchCnt.chargeFlg=0 AND tContractPlanDetail.contractTypeId = "allDepo", 0, tContractPlanDetail.searchUnitPrice)) as unitPriceAry'),
            $connection->raw('group_concat(IFNULL(searchCnt.searchCount, 0)) as countAry'),
        );
        $searchInfo->leftJoin('mContractPlan', function ($join){
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $searchInfo->leftJoinSub($searchCnt, 'searchCnt', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'searchCnt.companyId');
            $join->on('mContractPlan.planType', '=', 'searchCnt.planType');
            $join->on('tContractPlanDetail.contractStartDate', '<=', 'searchCnt.searchDate');
            $join->on('tContractPlanDetail.contractEndDate', '>=', 'searchCnt.searchDate');
        });
        $searchInfo->where('mContractPlan.planType', $type);
        $searchInfo->groupBy([
            'tContractPlanDetail.companyId',
            'planType',
        ]);

        return $searchInfo;

    }
}
