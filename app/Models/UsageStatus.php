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
 * ユーザーマスタ
 */
class UsageStatus extends Report
{
    use HasFactory;

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';
    
    const DATE_LOW_VALUE = '2000-01-01';
    const DATE_HIGH_VALUE = '3000-01-01';


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
            DB::raw('1 as searchCount'),
            'searchDate',
            'chargeFlg',
        );
        if($startDate != '' && $endDate != ''){
            $searchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }

        $dupSearchCnt = DB::table('tKeywordHistoryDetail');
        $dupSearchCnt->select(
            'companyId',
            DB::raw('sum(searchCount) as dupSearchCount'),
        );
        if($startDate != '' && $endDate != ''){
            $dupSearchCnt->whereBetween('searchDate', [$startDate, $endDate]);
        }
        $dupSearchCnt->groupBy([
            'companyId',
        ]);

        //検索単価
        $searchInfo = DB::table('tContractPlanDetail');
        $searchInfo->select(
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.contractPlanId',
            DB::raw('sum(searchCnt.searchCount) as totalCount'),
            //chargeFlg = 0 かつ 全額デポジット は検索単価0で集計
            DB::raw('group_concat(IF(searchCnt.chargeFlg=0 AND tContractPlanDetail.contractTypeId = "allDepo", 0, tContractPlanDetail.searchUnitPrice)) as unitPriceAry'),
            DB::raw('group_concat(searchCnt.searchCount) as countAry'),
        );
        $searchInfo->joinSub($searchCnt, 'searchCnt', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'searchCnt.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'searchCnt.contractPlanId');
            $join->on('tContractPlanDetail.contractStartDate', '<=', 'searchCnt.searchDate');
            $join->on('tContractPlanDetail.contractEndDate', '>=', 'searchCnt.searchDate');
        });
        $searchInfo->groupBy([
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.contractPlanId',
        ]);

        $trialInfo = DB::table('tContractPlan');
        $trialInfo->select(
            'tContractPlan.companyId',
            'tContractPlan.contractPlanId',
            'tContractPlan.trialSearchUnitPrice',
            DB::raw('sum(searchCnt.searchCount) as totalCount'),
        );
        $trialInfo->leftJoinSub($searchCnt, 'searchCnt', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchCnt.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'searchCnt.contractPlanId');
            $join->on('tContractPlan.startTrial', '<=', 'searchCnt.searchDate');
            $join->on('tContractPlan.useStartDate', '>', 'searchCnt.searchDate');
        });
        $trialInfo->groupBy([
            'tContractPlan.companyId',
            'tContractPlan.contractPlanId',
            'tContractPlan.trialSearchUnitPrice',
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
            'searchInfo.countAry as countAry',
            'trialInfo.totalCount as trialTotalCount',
        );
        $webPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->leftJoinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->leftJoinSub($searchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'searchInfo.contractPlanId');
        });
        $webPlan->leftJoinSub($trialInfo, 'trialInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'trialInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'trialInfo.contractPlanId');
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
            'searchInfo.countAry as countAry',
            'trialInfo.totalCount as trialTotalCount',
        );
        $apiPlan->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $apiPlan->leftJoinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->leftJoinSub($searchInfo, 'searchInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'searchInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'searchInfo.contractPlanId');
        });
        $apiPlan->leftJoinSub($trialInfo, 'trialInfo', function($join){
            $join->on('tContractPlan.companyId', '=', 'trialInfo.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'trialInfo.contractPlanId');
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
            DB::raw('IFNULL(webPlan.totalCount, 0) + IFNULL(apiPlan.totalCount, 0) + IFNULL(webPlan.trialTotalCount, 0) + IFNULL(apiPlan.trialTotalCount, 0) as sumCount'),
            'mContractStatus.name as statusName',
            DB::raw('IFNULL(dupSearchCnt.dupSearchCount, 0) as dupSearchCount'),
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

        $user->leftJoin('mContractStatus', function($join){
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
    public function calcUserListData($userList): array
    {
        $vatModel = new MVat();

        $retAry= [];
        $retAry['contractCom'] = 0;
        $retAry['trialCom'] = 0;
        $retAry['contractEndCom'] = 0;
        $retAry['sumSearchCount'] = 0;
        $retAry['sumId'] = 0;
        $retAry['sumPrice'] = 0;
        $retAry['sumPriceWithTax'] = 0;

        foreach($userList as $item){
            $item->webPlanUnitPriceAry = explode(",", $item->webPlanUnitPriceAry);
            $item->webPlanCountAry = explode(",", $item->webPlanCountAry);
            $item->apiPlanUnitPriceAry = explode(",", $item->apiPlanUnitPriceAry);
            $item->apiPlanCountAry = explode(",", $item->apiPlanCountAry);

            $webTotalPrice = 0;
            $apiTotalPrice = 0;

            //合計金額(会社・単価別)
            foreach($item->webPlanCountAry as $idx => $count){

                if($count == ''){
                    continue;
                }

                $webTotalPrice += $count * $item->webPlanUnitPriceAry[$idx];
            }

            foreach($item->apiPlanCountAry as $idx => $count){

                if($count == ''){
                    continue;
                }

                $apiTotalPrice += $count * $item->apiPlanUnitPriceAry[$idx];
            }


            $webTotalPrice += $item->webPlanTrialTotalCount * $item->webPlanTrialSearchUnitPrice;
            $apiTotalPrice += $item->apiPlanTrialTotalCount * $item->apiPlanTrialSearchUnitPrice;
            
            $item->webTotalPrice = $webTotalPrice;
            $item->apiTotalPrice = $apiTotalPrice;
            
            $retAry['sumSearchCount'] += $item->webPlanTotalCount;
            $retAry['sumSearchCount'] += $item->apiPlanTotalCount;
            $retAry['sumPrice'] += $item->webTotalPrice;
            $retAry['sumPrice'] += $item->apiTotalPrice;

            $webIds = is_null($item->webPlanIds) ? 0 : $item->webPlanIds;
            $apiIds = is_null($item->apiPlanIds) ? 0 : $item->apiPlanIds;
            $retAry['sumId'] += $webIds + $apiIds;

            //契約中/トライアル中/契約終了
            switch($item->contractStatus){
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

        $startDate = empty($startDate) ? self::DATE_LOW_VALUE : $startDate;
        $endDate = empty($endDate) ? self::DATE_HIGH_VALUE : $endDate;
    
        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);

        $retAry = [];

        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);

        $totalSearchCount= 0;
        $totalPrice= 0;
        $totalDupSearchCount = 0;
        $retAry['report'] = [];
        //利用状況詳細は現在までのすべての検索情報を取得
        $retAry['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, null, null);

        //トライアル時の検索数情報
        //WEB
        if(!is_null($apiPlanInfo)){

            $webEndTrial = date("Y-m-d",strtotime($webPlanInfo['useStartDate']."-1 day"));
            $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webPlanInfo['startTrial'], $webEndTrial, true);

            //トライアル期間の検索がある場合
            if(!is_null($webTrialSearchList)){

                foreach($webTrialSearchList as $searchItem){

                    $unitPrice = $webPlanInfo['trialSearchUnitPrice'];;
                    $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                    $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $webPlanInfo['startTrial'], $webEndTrial);

                    $retAry['report'][] = [
                        'userId' => $searchItem['userId'],
                        'userName' => $searchItem['name'].' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $webPlanInfo['startTrial'],
                        'contractEndDate' => $webEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'dupCount' => $dupSearchCount,
                    ];

                    $totalSearchCount += $searchItem['searchCount'];
                    $totalPrice += $price;
                    $totalDupSearchCount += $dupSearchCount;
                }
            }
        }

        //API
        if(!is_null($apiPlanInfo)){

            $apiEndTrial = date("Y-m-d",strtotime($apiPlanInfo['useStartDate']."-1 day"));
            $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiPlanInfo['startTrial'], $apiEndTrial, true);

            //トライアル期間の検索がある場合
            if(!is_null($apiTrialSearchList)){

                foreach($apiTrialSearchList as $searchItem){
                        
                    $unitPrice = $apiPlanInfo['trialSearchUnitPrice'];
                    $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];
                    $dupSearchCount = $tKeywordHistoryDetail->getSearchCount($companyId, $searchItem['userId'], $apiPlanInfo['startTrial'], $apiEndTrial);

                    $retAry['report'][] = [
                        'userId' => $searchItem['userId'],
                        'userName' => $searchItem['name'].' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $apiPlanInfo['startTrial'],
                        'contractEndDate' => $apiEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'dupCount' => $dupSearchCount,
                    ];

                    $totalSearchCount += $searchItem['searchCount'];
                    $totalPrice += $price;
                    $totalDupSearchCount += $dupSearchCount;
                }
            }
        }

        //プラン別ループ(tContractPlanDetail)
        foreach($retAry['contractInfo'] as $contractItem){
            
            $contractStartDate = $contractItem->contractStartDate;
            $contractEndDate = $contractItem->contractEndDate;

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
                ];

                $totalSearchCount += $searchItem['searchCount'];
                $totalPrice += $price;
                $totalDupSearchCount += $dupSearchCount;

            }

            $retAry['report'] = array_merge($retAry['report'], $wkAry);
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
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output( $fileName, "I" );
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
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output( $fileName, "I" );
    }

    /**
     * ファイル名を取得
     * @param $companyId
     * @return string
     */
    public function getFileName($companyId): string
    {
        if(is_null($companyId)){
            $fileName = '利用状況一覧.pdf';
            return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');

        }else{

            $fileName = '利用状況一覧-%s.pdf';
            return mb_convert_encoding(sprintf($fileName, $companyId), 'SJIS-WIN', 'UTF-8');
        }
    }
    
}
