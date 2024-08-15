<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

/**
 * 利用状況
 */
class AgentUsageStatus extends BaseModel
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
     * @return LengthAwarePaginator|Collection
     */
    public function getList($pageLine, $contractPlan, $chargeName, $dispType, $startDate, $endDate, bool $paginateFlg, $agentNo = null): LengthAwarePaginator|Collection
    {
        $this->agentDBConnection = $this->getAgentDBConnection($agentNo);

        //ID数 クエリ
        $connection = DB::connection($this->agentDBConnection);
        $idNum = $connection->table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        $idNum->groupBy(['companyId', 'contractPlanId']);

        // 検索数取得クエリを生成
        $webSearchCnt = $this->buildQuerySearchCnt(self::PLAN_TYPE_WEB, $startDate, $endDate);
        $apiSearchCnt = $this->buildQuerySearchCnt(self::PLAN_TYPE_API, $startDate, $endDate);

        // WEBプラン クエリ
        $connection = DB::connection($this->agentDBConnection);
        $webPlan = $connection->table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'webPlanIds.ids',
            'searchCnt.searchCount as totalSearchCount',
        );
        $webPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->leftJoinSub($idNum, 'webPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->leftJoinSub($webSearchCnt, 'searchCnt', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'searchCnt.companyId');
            $join->on('mContractPlan.planType', '=', 'searchCnt.planType');
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
            'searchCnt.searchCount as totalSearchCount',
        );
        $apiPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->leftJoinSub($idNum, 'apiPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->leftJoinSub($apiSearchCnt, 'searchCnt', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'searchCnt.companyId');
            $join->on('mContractPlan.planType', '=', 'searchCnt.planType');
        });
        $apiPlan->where('mContractPlan.planType', self::PLAN_TYPE_API);

        // Acurisプラン クエリ
        $connection = DB::connection($this->agentDBConnection);
        $acurisSearchCnt = $connection->table('tAcurisKeywordHistory');
        $acurisSearchCnt->select(
            'companyId',
            DB::raw('SUM(searchCount) as searchCount'),
            DB::raw('SUM(lookupCount) as lookupCount'),
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
            'mUserCompany.companyId',
            'mUserCompany.name',
            'mUserCompany.delFlg',
            // web
            'webPlan.useStartDate as webUseStartDate',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.name as webPlanName',
            DB::raw('IFNULL(webPlan.ids, 0) as webPlanIds'),
            DB::raw('IFNULL(webPlan.totalSearchCount, 0) as webSearchCount'),
            // api
            'apiPlan.useStartDate as apiUseStartDate',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.name as apiPlanName',
            DB::raw('IFNULL(apiPlan.ids, 0) as apiPlanIds'),
            DB::raw('IFNULL(apiPlan.totalSearchCount, 0) as apiSearchCount'),
            // acuris
            DB::raw('IFNULL(acurisSearchCnt.searchCount, 0) as acurisTotalCount'),
            DB::raw('IFNULL(acurisSearchCnt.lookupCount, 0) as acurisDetailTotalCount'),
            // 合計検索数
            DB::raw('IFNULL(webPlan.totalSearchCount, 0) + IFNULL(apiPlan.totalSearchCount, 0) + IFNULL(acurisSearchCnt.searchCount, 0) + IFNULL(acurisSearchCnt.lookupCount, 0) as sumCount'),
        );

        $user->leftJoinSub($webPlan, 'webPlan', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });
        $user->leftJoinSub($apiPlan, 'apiPlan', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });
        $user->leftJoinSub($acurisSearchCnt, 'acurisSearchCnt', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'acurisSearchCnt.companyId');
        });
        $user->leftJoin('mContractStatus', function ($join) {
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        /* @var string $user */
        $connection = DB::connection($this->agentDBConnection);
        $query = $connection->table($user);
        $query->where('delFlg', self::DEL_FLG_OFF);

        if ($contractPlan != '') {
            $query->whereRaw('(webPlanPlanId = ? or apiPlanPlanId = ?)', [$contractPlan, $contractPlan]);
        }

        if ($chargeName != '') {
            $query->where('chargeName', 'like', '%' . $chargeName . '%');
        }

        if ($dispType == 1) {
            //検索件数 昇順
            $query->orderBy('sumCount');
        } else {
            //検索件数 降順
            $query->orderByDesc('sumCount');
        }

        if ($paginateFlg === true) {
            if ($pageLine == '') {
                $pageLine = self::PAGE_LINE;
            }
            $calcAry = $query->get();
            $retAry = $query->paginate($pageLine);
        } else {
            $calcAry = $query->get();
            $retAry = $query->get();
        }

        //各ユーザー毎合計計算
        $calcList = $this->calcUserListData($calcAry);
        $retList = $this->calcUserListData($retAry);
        $retData = $retList['userList'];

        //指定期間内集計一覧
        $retData->totalCom = $calcList['totalCom'];
        $retData->sumSearchCount = $calcList['sumSearchCount'];
        $retData->sumId = $calcList['sumId'];

        // 会社IDを暗号化
        foreach ($retData as $idx => $item) {
            $retData[$idx]->companyId = Crypt::encrypt($item->companyId);
        }

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
        $retAry = [];
        $retAry['totalCom'] = 0;
        $retAry['sumSearchCount'] = 0;
        $retAry['sumId'] = 0;

        foreach ($userList as $item) {

            $retAry['sumSearchCount'] += (int)$item->sumCount;

            $webIds = $item->webPlanIds;
            $apiIds = $item->apiPlanIds;
            $retAry['sumId'] += $webIds + $apiIds;

            $retAry['totalCom']++;
        }


        $retAry['userList'] = $userList;

        return $retAry;
    }

    /**
     * 詳細データ取得
     *
     * @param $companyId
     * @param $startDate
     * @param $endDate
     * @param int $agentNo
     * @return array
     */
    public function getDetailData($companyId, $startDate, $endDate, $agentNo = null): array
    {
        $this->agentDBConnection = $this->getAgentDBConnection($agentNo);

        $connection = DB::connection($this->agentDBConnection);
        $query = $connection->table('mUserDetail', 'mUD');
        $query->select(
            'mUD.userId',
            DB::raw("IFNULL(count(tKH.userId), 0) as searchCount"),
            DB::raw("IFNULL(SUM(tKHD.searchCount), 0) as dupSearchCount"),
        );
        $query->leftJoin('tKeywordHistory as tKH', function ($join) use ($startDate, $endDate) {
            $join->on('mUD.companyId', '=', 'tKH.companyId');
            $join->on('mUD.userId', '=', 'tKH.userId');
            $join->whereBetween('tKH.searchDate', [$startDate, $endDate]);
        });
        $query->leftJoin('tKeywordHistoryDetail as tKHD', function ($join) use ($startDate, $endDate) {
            $join->on('mUD.companyId', '=', 'tKHD.companyId');
            $join->on('mUD.userId', '=', 'tKHD.userId');
            $join->whereBetween('tKHD.searchDate', [$startDate, $endDate]);
        });
        $query->where('mUD.companyId', $companyId);
        $query->groupBy('mUD.userId');

        $dataList = $query->get();

        // 合計検索数 集計
        $totalSearchCount = 0;
        $totalDupSearchCount = 0;
        foreach ($dataList as $item) {
            $totalSearchCount += $item->searchCount;
            $totalDupSearchCount += $item->dupSearchCount;
        }

        $retAry = [
            'userIdList' => $dataList,
            'totalSearchCount' => $totalSearchCount,
            'totalDupSearchCount' => $totalDupSearchCount
        ];

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
            DB::raw('SUM(1) as searchCount'),
        );
        $searchCnt->leftJoin('mContractPlan', function ($join) {
            $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $searchCnt->where('planType', $type);
        if ($startDate != '' && $endDate != '') {
            $searchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $searchCnt->groupBy([
            'companyId',
            'mContractPlan.planType',
        ]);

        return $searchCnt;
    }
}
