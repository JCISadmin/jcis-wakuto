<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Support\Collection;
use TCPDF;

/**
 * 利用状況
 */
class UsageStatus extends BaseModel
{
    use HasFactory;

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
     * @return LengthAwarePaginator|Collection
     */
    public function getList($pageLine, $contractPlan, $chargeName, $dispType, $startDate, $endDate, bool $paginateFlg): LengthAwarePaginator|Collection
    {
        //ID数 クエリ
        $idNum = DB::table('mUserDetail');
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

        // 検索情報取得クエリを生成
        $webSearchInfo = $this->buildQuerySearchInfo(self::PLAN_TYPE_WEB, $webSearchCnt);
        $apiSearchInfo = $this->buildQuerySearchInfo(self::PLAN_TYPE_API, $apiSearchCnt);

        // トライアル検索情報取得クエリを生成
        $webTrialSearchInfo = $this->buildQueryTrialSearchInfo(self::PLAN_TYPE_WEB, $webSearchCnt);
        $apiTrialSearchInfo = $this->buildQueryTrialSearchInfo(self::PLAN_TYPE_API, $apiSearchCnt);

        // 同一ワード検索数 クエリ
        $dupSearchCnt = DB::table('tKeywordHistoryDetail');
        $dupSearchCnt->select(
            'companyId',
            DB::raw('sum(searchCount) as dupSearchCount'),
        );
        if ($startDate != '' && $endDate != '') {
            $dupSearchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $dupSearchCnt->groupBy([
            'companyId',
        ]);

        // WEBプラン クエリ
        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'webPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry as countAry',
            'trialInfo.totalCount as trialTotalCount',
        );
        $webPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->leftJoinSub($idNum, 'webPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->leftJoinSub($webSearchInfo, 'searchInfo', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'searchInfo.planType');
        });
        $webPlan->leftJoinSub($webTrialSearchInfo, 'trialInfo', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'trialInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'trialInfo.planType');
        });
        $webPlan->where('mContractPlan.planType', self::PLAN_TYPE_WEB);

        // APIプラン クエリ
        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'apiPlanIds.ids',
            'searchInfo.totalCount as totalCount',
            'searchInfo.unitPriceAry as unitPriceAry',
            'searchInfo.countAry as countAry',
            'trialInfo.totalCount as trialTotalCount',
        );
        $apiPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->leftJoinSub($idNum, 'apiPlanIds', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->leftJoinSub($apiSearchInfo, 'searchInfo', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'searchInfo.planType');
        });
        $apiPlan->leftJoinSub($apiTrialSearchInfo, 'trialInfo', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'trialInfo.companyId');
            $join->on('mContractPlan.planType', '=', 'trialInfo.planType');
        });
        $apiPlan->where('mContractPlan.planType', self::PLAN_TYPE_API);

        // Acurisプラン クエリ
        $acurisSearchCnt = DB::table('tAcurisKeywordHistory');
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
        $user = DB::table('mUserCompany');

        $user->select(
            'mUserCompany.*',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.name as webPlanName',
            'webPlan.useEndAlertDate as webPlanUseEndAlertDate',
            'webPlan.useEndDate as webPlanUseEndDate',
            'webPlan.ids as webPlanIds',
            DB::raw('IFNULL(webPlan.totalCount, 0) + IFNULL(webPlan.trialTotalCount, 0) as webPlanTotalCount'),
            'webPlan.unitPriceAry as webPlanUnitPriceAry',
            'webPlan.countAry as webPlanCountAry',
            'webPlan.trialTotalCount as webPlanTrialTotalCount',
            'webPlan.trialSearchUnitPrice as webPlanTrialSearchUnitPrice',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.name as apiPlanName',
            'apiPlan.useEndAlertDate as apiPlanUseEndAlertDate',
            'apiPlan.useEndDate as apiPlanUseEndDate',
            'apiPlan.ids as apiPlanIds',
            DB::raw('IFNULL(apiPlan.totalCount, 0) + IFNULL(apiPlan.trialTotalCount, 0) as apiPlanTotalCount'),
            'apiPlan.unitPriceAry as apiPlanUnitPriceAry',
            'apiPlan.countAry as apiPlanCountAry',
            'apiPlan.trialTotalCount as apiPlanTrialTotalCount',
            'apiPlan.trialSearchUnitPrice as apiPlanTrialSearchUnitPrice',
            DB::raw('IFNULL(acurisSearchCnt.searchCount, 0) as acurisTotalCount'),
            DB::raw('IFNULL(acurisSearchCnt.lookupCount, 0) as acurisDetailTotalCount'),
            DB::raw('IFNULL(webPlan.totalCount, 0) + IFNULL(apiPlan.totalCount, 0) + IFNULL(webPlan.trialTotalCount, 0) + IFNULL(apiPlan.trialTotalCount, 0) + IFNULL(acurisSearchCnt.searchCount, 0) + IFNULL(acurisSearchCnt.lookupCount, 0) as sumCount'),
            'mContractStatus.name as statusName',
            DB::raw('IFNULL(dupSearchCnt.dupSearchCount, 0) as dupSearchCount'),
        );

        $user->leftJoinSub($webPlan, 'webPlan', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });

        $user->leftJoinSub($apiPlan, 'apiPlan', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });

        $user->leftJoinSub($dupSearchCnt, 'dupSearchCnt', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'dupSearchCnt.companyId');
        });

        $user->leftJoinSub($acurisSearchCnt, 'acurisSearchCnt', function ($join) {
            $join->on('mUserCompany.companyId', '=', 'acurisSearchCnt.companyId');
        });

        $user->leftJoin('mContractStatus', function ($join) {
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        /* @var string $user */
        $query = DB::table($user);
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
        $retData->contractCom = $calcList['contractCom'];
        $retData->trialCom = $calcList['trialCom'];
        $retData->contractEndCom = $calcList['contractEndCom'];
        $retData->sumSearchCount = $calcList['sumSearchCount'];
        $retData->sumId = $calcList['sumId'];
        $retData->sumPrice = $calcList['sumPrice'];
        $retData->sumPriceWithTax = $calcList['sumPriceWithTax'];

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

        $retAry = [];
        $retAry['contractCom'] = 0;
        $retAry['trialCom'] = 0;
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

            foreach ($item->apiPlanCountAry as $idx => $count) {

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

            // トライアル分
            $webTotalPrice += (int)$item->webPlanTrialTotalCount * (int)$item->webPlanTrialSearchUnitPrice;
            $apiTotalPrice += (int)$item->apiPlanTrialTotalCount * (int)$item->apiPlanTrialSearchUnitPrice;

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
            switch ($item->contractStatus) {
                case 1:
                    $retAry['trialCom']++;
                    break;
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
     * レポート用データ取得:期間指定(請求詳細画面用)
     *
     * @param $companyId
     * @param $startDate
     * @param $endDate
     * @return array
     */
    public function getReportDataByPeriod($companyId, $startDate, $endDate): array
    {
        $keywordModel = new TKeywordHistory();
        $acurisKeywordModel = new TAcurisKeywordHistory();
        $tKeywordHistoryDetail = new TKeywordHistoryDetail();
        $mUserDetailModel = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();

        // 現在は全件取得のため開始日終了日は使用なし
        $startDate = empty($startDate) ? self::DATE_LOW_VALUE : $startDate;
        $endDate = empty($endDate) ? self::DATE_HIGH_VALUE : $endDate;

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);

        $retAry = [];

        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);

        $totalSearchCount = 0;
        $totalPrice = 0;
        $totalDupSearchCount = 0;
        $retAry['report'] = [];
        //利用状況詳細は現在までのすべての検索情報を取得
        $retAry['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, null, null);

        //トライアル時の検索数情報
        //WEB
        if (!is_null($webPlanInfo)) {

            if (!is_null($webPlanInfo['useStartDate'])) {
                $webEndTrial = date("Y-m-d", strtotime($webPlanInfo['useStartDate'] . "-1 day"));
            } else {
                $webEndTrial = date("Y-m-d");
            }

            $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webPlanInfo['startTrial'], $webEndTrial, true);

            //トライアル期間の検索がある場合
            if (!is_null($webTrialSearchList)) {

                foreach ($webTrialSearchList as $searchItem) {

                    $unitPrice = empty($webPlanInfo['trialSearchUnitPrice']) ? 0 : $webPlanInfo['trialSearchUnitPrice'];
                    $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                    $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $webPlanInfo['startTrial'], $webEndTrial);

                    $retAry['report'][] = [
                        'userId' => $searchItem['userId'],
                        'userName' => $searchItem['name'] . ' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $webPlanInfo['startTrial'],
                        'contractEndDate' => $webEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'dupCount' => $dupSearchCount,
                        'type' => 'normal',
                    ];

                    $totalSearchCount += $searchItem['searchCount'];
                    $totalPrice += $price;
                    $totalDupSearchCount += $dupSearchCount;
                }
            }
        }

        //API
        if (!is_null($apiPlanInfo)) {

            if (!is_null($apiPlanInfo['useStartDate'])) {
                $apiEndTrial = date("Y-m-d", strtotime($apiPlanInfo['useStartDate'] . "-1 day"));
            } else {
                $apiEndTrial = date("Y-m-d");
            }

            $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiPlanInfo['startTrial'], $apiEndTrial, true);

            //トライアル期間の検索がある場合
            if (!is_null($apiTrialSearchList)) {

                foreach ($apiTrialSearchList as $searchItem) {

                    $unitPrice = empty($apiPlanInfo['trialSearchUnitPrice']) ? 0 : $apiPlanInfo['trialSearchUnitPrice'];
                    $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                    $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $apiPlanInfo['startTrial'], $apiEndTrial);

                    $retAry['report'][] = [
                        'userId' => $searchItem['userId'],
                        'userName' => $searchItem['name'] . ' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $apiPlanInfo['startTrial'],
                        'contractEndDate' => $apiEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'dupCount' => $dupSearchCount,
                        'type' => 'normal',
                    ];

                    $totalSearchCount += $searchItem['searchCount'];
                    $totalPrice += $price;
                    $totalDupSearchCount += $dupSearchCount;
                }
            }
        }

        //プラン別ループ(tContractPlanDetail)
        foreach ($retAry['contractInfo'] as $contractItem) {

            $contractStartDate = $contractItem->contractStartDate;
            $contractEndDate = $contractItem->contractEndDate;

            //検索数情報
            $searchList = $keywordModel->getSearchCountByReport($companyId, $userIds[$contractItem->planType], $contractItem->planType, $contractStartDate, $contractEndDate);
            $wkAry = [];

            foreach ($searchList as $searchItem) {

                $depositName = '';

                //全額デポジット かつ chargeFlg=0 は検索料金無し
                if ($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE) {
                    $unitPrice = 0;
                    $price = 0;
                    $depositName = ' (デポジット内)';
                } else {
                    $unitPrice = empty($contractItem->searchUnitPrice) ? 0 : $contractItem->searchUnitPrice;
                    $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                }
                $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $contractStartDate, $contractEndDate);

                $wkAry[] = [
                    'userId' => $searchItem['userId'],
                    'userName' => $searchItem['name'] . $depositName,
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
        $acurisSearchData = $acurisKeywordModel->getSearchDataByUserId($companyId, NULL, NULL);

        $wkAcurisAry = [];
        foreach ($acurisSearchData as $searchItem) {

            // アキュリス検索(一覧)
            $unitPrice = config('hds.acuris.search.normal.unitPrice');
            $count = $searchItem->searchCount;
            $price = $unitPrice * $count;

            if ($count > 0) {
                $acurisName = ' (' . config('hds.acuris.search.normal.title') . ')';
                $wkAcurisAry[] = [
                    'userId' => $searchItem->userId,
                    'userName' => $mUserDetailModel->getUserName($companyId, $searchItem->userId) . $acurisName,
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

            if ($count > 0) {
                $acurisName = ' (' . config('hds.acuris.search.detail.title') . ')';
                $wkAcurisAry[] = [
                    'userId' => $searchItem->userId,
                    'userName' => $mUserDetailModel->getUserName($companyId, $searchItem->userId) . $acurisName,
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
        if (!is_null($retAry['report'])) {
            array_multisort($userIdSortAry, SORT_ASC, $typeSortAry, SORT_DESC, $retAry['report']);
        }

        $retAry['totalSearchCount'] = $totalSearchCount;
        $retAry['totalPrice'] = $totalPrice;
        $retAry['totalDupSearchCount'] = $totalDupSearchCount;

        return $retAry;
    }

    /**
     * PDF生成(利用状況一覧)
     *
     * @param $companyId
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function makeListPdf($fileName, $cond, $pageNum): string
    {
        $userList = $this->getList(
            $pageNum,
            $cond['contractPlan'],
            $cond['chargeName'],
            $cond['dispType'],
            $cond['searchDateFrom'],
            $cond['searchDateTo'],
            true
        );

        $pdfData = [
            'userList' => $userList,
        ];

        //PDF生成
        $pdfTemplate = 'pdf.pdfUsageStatusList';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8");
        $pdf->SetFont('kozminproregular', '', 9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output($fileName, "I");
    }



    /**
     * PDF生成(利用状況詳細)
     *
     * @param $companyId
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function makeDetailPdf($fileName, $companyId, $startDate, $endDate): string
    {
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        $model = new UsageStatus();
        $detail = $model->getReportDataByPeriod($companyId, $startDate, $endDate);

        $pdfData = [
            'companyName' => $companyName,
            'detail' => $detail,
        ];

        //PDF生成
        $pdfTemplate = 'pdf.pdfUsageStatusDetail';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, "UTF-8");
        $pdf->SetFont('kozminproregular', '', 9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output($fileName, "I");
    }

    /**
     * ファイル名を取得
     * @param $companyId
     * @return string
     */
    public function getFileName($companyId): string
    {
        if (is_null($companyId)) {
            $fileName = '利用状況一覧.pdf';
            return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
        } else {

            $fileName = '利用状況一覧-%s.pdf';
            return mb_convert_encoding(sprintf($fileName, $companyId), 'SJIS-WIN', 'UTF-8');
        }
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
        $searchCnt = DB::table('tKeywordHistory');
        $searchCnt->select(
            'companyId',
            'mContractPlan.planType as planType',
            DB::raw('1 as searchCount'),
            'searchDate',
            'chargeFlg',
        );
        $searchCnt->leftJoin('mContractPlan', function ($join) {
            $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $searchCnt->where('planType', $type);
        if ($startDate != '' && $endDate != '') {
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
        $searchInfo = DB::table('tContractPlanDetail');
        $searchInfo->select(
            'tContractPlanDetail.companyId',
            'mContractPlan.planType as planType',
            DB::raw('sum(searchCnt.searchCount) as totalCount'),
            //chargeFlg = 0 かつ 全額デポジット は検索単価0で集計
            DB::raw('group_concat(IF(searchCnt.chargeFlg=0 AND tContractPlanDetail.contractTypeId = "allDepo", 0, tContractPlanDetail.searchUnitPrice)) as unitPriceAry'),
            DB::raw('group_concat(IFNULL(searchCnt.searchCount, 0)) as countAry'),
        );
        $searchInfo->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $searchInfo->leftJoinSub($searchCnt, 'searchCnt', function ($join) {
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

    /**
     * トライアル検索情報取得クエリを生成
     * @param $type
     * @param $searchCnt
     * @return
     */
    private function buildQueryTrialSearchInfo($type, $searchCnt)
    {
        $trialInfo = DB::table('tContractPlan');
        $trialInfo->select(
            'tContractPlan.companyId',
            'mContractPlan.planType as planType',
            'tContractPlan.trialSearchUnitPrice',
            DB::raw('sum(searchCnt.searchCount) as totalCount'),
        );
        $trialInfo->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $trialInfo->leftJoinSub($searchCnt, 'searchCnt', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'searchCnt.companyId');
            $join->on('mContractPlan.planType', '=', 'searchCnt.planType');
            $join->on('tContractPlan.startTrial', '<=', 'searchCnt.searchDate');
            $join->on('tContractPlan.useStartDate', '>', 'searchCnt.searchDate');
        });
        $trialInfo->where('mContractPlan.planType', $type);
        $trialInfo->groupBy([
            'tContractPlan.companyId',
            'mContractPlan.planType',
            'tContractPlan.trialSearchUnitPrice',
        ]);

        return $trialInfo;
    }
}
