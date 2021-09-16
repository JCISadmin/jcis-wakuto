<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use App\Models\TKeywordHistory;
use App\Models\MContractPlan;
use App\Models\TContractPlan;

class TClaim extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tClaim';

    const TYPE_ALL_DEPOSIT = 1;
    const TYPE_ID_DEPOSIT = 2;
    const TYPE_MONTHLY = 3;
    const PLAN_TRAIAL = 4;

    /**
     * 請求情報を取得
     *
     * @param $claimMonth
     * @param $pageLine
     * @return $list
     */
    public function getList($claimMonth, $companyName = null, $companyIds = null, $pagenateFlg = false, $pageLine = '')
    { 
        $year = date_format(new DateTime($claimMonth), 'Y');
        $month = date_format(new DateTime($claimMonth), 'm');
        $strClaimMonth = str_replace('-', '', $claimMonth);
    
        $idNum = DB::table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        $idNum->groupBy(['companyId', 'contractPlanId']);

        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'webPlanIds.ids',
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->join('mContractType', function ($join) {
            $join->on('tContractPlan.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $webPlan->joinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->where('mContractPlan.planType', 'web');

        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'apiPlanIds.ids',
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->join('mContractType', function ($join) {
            $join->on('tContractPlan.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $apiPlan->joinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->where('mContractPlan.planType', 'api');

        $claim = DB::table('tClaim');
        $claim->select(
            'tClaim.*',
        );
        $claim->where('claimMonth', $strClaimMonth);

        
        $user = DB::table('mUserCompany');
        $user->select(
            'mUserCompany.*',
            'webPlan.companyId as webPlanCompanyId',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.contractPlanName as webPlanPlanName',
            'webPlan.contractTypeId as webPlanTypeId',
            'webPlan.contractTypeName as webPlanTypeName',
            'webPlan.planType as webPlanPlanType',
            'webPlan.ids as webPlanIds',
            'webPlan.deposit as webPlanDeposit',
            'webPlan.idUnitPrice as webPlanIdUnitPrice',
            'webPlan.searchUnitPrice as webPlanSearchUnitPrice',
            'apiPlan.companyId as apiPlanCompanyId',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.contractPlanName as apiPlanPlanName',
            'apiPlan.contractTypeId as apiPlanTypeId',
            'apiPlan.contractTypeName as apiPlanTypeName',
            'apiPlan.idUnitPrice as apiPlanIdUnitPrice',
            'apiPlan.searchUnitPrice as apiPlanSearchUnitPrice',
            'apiPlan.planType as apiPlanPlanType',
            'apiPlan.ids as apiPlanIds',
            'apiPlan.deposit as apiPlanDeposit',
            'mContractStatus.name as statusName',
            'claim.claimNo',
            'claim.claimStatus',
            'claim.paymentStatus',
            'claim.claimDate',
            'claim.paymentDate',
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

        $user->leftJoinSub($claim, 'claim', function($join){
            $join->on('mUserCompany.companyId', '=', 'claim.companyId');
        });

        $user->orderBy('mUserCompany.companyId');

        
       
        /* @var string $user */
        $query = DB::table($user);
        $query->where('delFlg', self::DEL_FLG_OFF);

        if(is_null($companyName) === false){
            $query->where('name', $companyName);
        }

        if(is_null($companyIds) === false){
            $idAry = [];
            foreach($companyIds as $kay => $id){
                $idAry[] = $id; 
            }
            $query->whereIn('companyId', $idAry);
        }

        if($pagenateFlg === true){
            if ($pageLine == '') {
                $pageLine = self::PAGE_LINE;
            }
            $list = $query->paginate($pageLine);
        }else{
            $query->where('claimStatus', self::PAYMENT_STATUS_DONE);
            $list = $query->get();
        }

        foreach($list as $key => $items){
            $model = new TKeywordHistory();
            $list[$key]->webPlanSearchCount = $model->getMonthSearchCount($items->webPlanCompanyId, null, $items->webPlanPlanId, $year, $month);
            $list[$key]->apiPlanSearchCount = $model->getMonthSearchCount($items->apiPlanCompanyId, null, $items->apiPlanPlanId, $year, $month);
            $webPrice = $this->getPrice($claimMonth, $items, $items->webPlanPlanType);
            $apiPrice = $this->getPrice($claimMonth, $items, $items->apiPlanPlanType);
            $list[$key]->webPrice = $webPrice;
            $list[$key]->apiPrice = $apiPrice;
            $list[$key]->price = $webPrice['totalPrice'] + $apiPrice['totalPrice'];
        }
        return $list;
    }

    /**
     * 請求ステータスを請求済に変更
     *
     * @param $companyId
     * @param $claimMonth
     */
    public function changeClaimStatus($companyId, $claimMonth)
    {
        $this->begin();
        
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $claimMonth = str_replace('-', '', $claimMonth);
        
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $count = $query->first();

        if($count->count > 0){
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $upd->where('claimMonth', $claimMonth);
            $upd->update([
                'claimDate' => $now,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'updateDatetime' => $now,
            ]);

        }else{
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'claimMonth' => $claimMonth,
                'claimNo' => '',
                'price' => '',
                'claimDate' => $now,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                'updateDatetime' => $now,
                'updateDatetime' => $now,
            ]);
        }

        $this->commit();

    }

    /**
     * 入金ステータスを入金済に変更
     *
     * @param $companyId
     * @param $claimMonth
     */
    public function changePaymentStatus($companyId, $claimMonth)
    {
        $this->begin();
        
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $claimMonth = str_replace('-', '', $claimMonth);
        
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $query->update([
            'paymentStatus' => self::PAYMENT_STATUS_DONE,
            'updateDatetime' => $now,
        ]);

        $this->commit();
    }

     /**
     * 請求情報を取得
     *
     * @param $claimMonth
     * @param $data
     * @param $planType
     * @return $price
     */

    public function getPrice($claimMonth, $data, $planType)
    {
        $mContractPlanModel = new MContractPlan();
        $keywordHistoryModel = new TKeywordHistory();
        $tContractPlanModel = new TContractPlan();
        $trialPrice = 0;
        $idPrice = 0;
        $depositPrice = 0;
        $payPerUse = 0;
        $totalPrice = 0;
        $price = [
            'trialPrice'=> $trialPrice,//トライアル費用
            'idPrice' => $idPrice,//ID代
            'depositPrice' => $depositPrice,//デポジット代
            'payPerUse' => $payPerUse,//従量課金
            'totalPrice' => $totalPrice,//合計額
        ];

        //key:契約プラン value:契約プラン設定
        $planList = $mContractPlanModel->getSelectList();
        foreach($planList as $items){            
            $planInfo[$items->contractPlanId] = $items;
        }

        //契約情報
        $detail = $tContractPlanModel->getPlan($data->companyId, $planType);
        if(is_null($detail)){
            return $price;
        }

        //トライアル単価
        $trialUnitPrice = $planInfo[self::PLAN_TRAIAL]->unitPrice === null ? 0 : $planInfo[self::PLAN_TRAIAL]->unitPrice;

        //請求月
        $claimMonth;
        $objClaimMonth = new DateTime($claimMonth);
        //請求月の月初日
        $firstDate = date('Y-m-d', strtotime('first day of ' . $claimMonth));
        //請求月の月末日
        $lastDate = date('Y-m-d', strtotime('last day of ' . $claimMonth));
        //請求月の前月
        $claimPrevMonth = $objClaimMonth->modify("-1 month")->format('Y-m');
        //請求月の次月
        $claimNextMonth = $objClaimMonth->modify('+2 month')->format('Y-m');
        //トライアル開始月
        $traialMonth = null;
        //利用開始月
        $startMonth = null;

        //トライアル期間検索数：請求月
        if(is_null($detail['startTrial']) || is_null($detail['useStartDate'])){
            $trialSearchCount = 0;
        }else{
            $startTrial = null;
            $endTrial = null;
            $traialMonth = date_format(new DateTime($detail['startTrial']), 'Y-m');
            $startMonth = date_format(new DateTime($detail['useStartDate']), 'Y-m');
            
            //請求月のトライアル期間：開始
            if(strtotime($traialMonth) === strtotime($claimMonth)){
                $startTrial = $detail['startTrial'];
            }elseif(strtotime($traialMonth) === strtotime($claimPrevMonth)){
                $startTrial = $firstDate;
            }

            //請求月のトライアル期間：終了
            if(strtotime($startMonth) === strtotime($claimNextMonth)){
                $endTrial = $lastDate;
            }elseif(strtotime($startMonth) === strtotime($claimMonth)){
                $useStartDate = new DateTime($detail['useStartDate']);
                $endTrial = $useStartDate->modify("-1 day");
            }

            if(is_null($startTrial) || is_null($endTrial)){
                $trialSearchCount = 0;
            }else{
                $trialSearchCount = $keywordHistoryModel->getSearchCount($data->companyId, $detail['contractPlanId'], null, $startTrial, $endTrial);
            }
        }

        //契約期間検索数:請求月
        if(is_null($detail['useStartDate']) || is_null($detail['useEndDate'])){
            $trialSearchCount = 0;
        }else{
            //請求月の契約期間：開始
            if(strtotime($firstDate) > strtotime($detail['useStartDate'])){
                $startDate = $firstDate;
            }else{
                $startDate = $detail['useStartDate'];
            }
            //請求月の契約期間：終了
            if(strtotime($lastDate) < strtotime($detail['useEndDate'])){
                $endDate = $lastDate;
            }else{
                $endDate = $detail['useEndDate'];
            }

            $actuallySearchCount = $keywordHistoryModel->getSearchCount($data->companyId, $detail['contractPlanId'], null, $startDate, $endDate);
        }

        //ID単価
        $idUnitPrice = $detail['idUnitPrice'] === null ? 0 : $detail['idUnitPrice'];

        //検索単価
        $searchUnitPrice = $detail['searchUnitPrice'] === null ? 0 : $detail['searchUnitPrice'];

        //年間検索数
        $searchCount = $detail['searchCount'] === null ? 0 : $detail['searchCount'];

        //デポジット残高
        $deposit = $detail['deposit'] === null ? 0 : $detail['deposit'];

        //契約更新月
        $useUpdateMonth = $detail['useUpdateDate'] === null ? $detail['useUpdateDate'] : date_format(new DateTime($detail['useUpdateDate']), 'Y-m');
        
        if(is_null($traialMonth) || is_null($startMonth)){
            return $price;
        }

        //全額デポジット
        switch ($detail['contractTypeId']){
            //全額デポジット
            case self::TYPE_ALL_DEPOSIT:
                if(strtotime($traialMonth) === strtotime($claimMonth)){
                    $trialPrice = $trialUnitPrice * $trialSearchCount;

                    if(strtotime($startMonth) === strtotime($claimNextMonth)){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                        $depositPrice = $searchUnitPrice * $searchCount;
                    }

                }elseif(strtotime($startMonth) === strtotime($claimMonth) || strtotime($useUpdateMonth) === strtotime($claimMonth)){
                    $trialPrice = $trialUnitPrice * $trialSearchCount;
                    $payPerUse = $searchUnitPrice * $actuallySearchCount - $deposit;

                    if($payPerUse < 0){
                        $payPerUse = 0;
                    }

                    $claim = false;
                    $claim = $this->getClaimStatus($data->companyId, $startMonth);
                    $claim = $this->getClaimStatus($data->companyId, $useUpdateMonth);

                    if($claim === false){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                        $depositPrice = $searchUnitPrice * $searchCount;
                    }

                }else{
                    $payPerUse = $searchUnitPrice * $actuallySearchCount - $deposit;

                    if($payPerUse < 0){
                        $payPerUse = 0;
                    }

                    if(strtotime($startMonth) === strtotime($claimNextMonth) || strtotime($useUpdateMonth) === strtotime($claimNextMonth)){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                        $depositPrice = $searchUnitPrice * $searchCount;
                    }

                }

                $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;
                $price = [
                    'trialPrice'=> $trialPrice,
                    'idPrice' => $idPrice,
                    'depositPrice' => $depositPrice,
                    'payPerUse' => $payPerUse,
                    'totalPrice' => $totalPrice,
                ];
                return $price;

            //ID代のみデポジット
            case self::TYPE_ID_DEPOSIT:
                if(strtotime($traialMonth) === strtotime($claimMonth)){
                    $trialPrice = $trialUnitPrice * $trialSearchCount;

                    if(strtotime($startMonth) === strtotime($claimNextMonth)){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                    }

                }elseif(strtotime($startMonth) === strtotime($claimMonth) || strtotime($useUpdateMonth) === strtotime($claimMonth)){
                    $payPerUse = $trialUnitPrice * $trialSearchCount + $searchUnitPrice * $actuallySearchCount;

                    $claim = false;
                    $claim = $this->getClaimStatus($data->companyId, str_replace('-','',$startMonth));                        
                    $claim = $this->getClaimStatus($data->companyId, $useUpdateMonth);

                    if($claim === false){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                    }

                }else{
                    $payPerUse = $searchUnitPrice * $actuallySearchCount;

                    if(strtotime($startMonth) === strtotime($claimNextMonth) || strtotime($useUpdateMonth) === strtotime($claimNextMonth)){
                        $idPrice = $idUnitPrice * $detail['ids'] * 12;
                    }
                }

                $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;
                $price = [
                    'trialPrice'=> $trialPrice,
                    'idPrice' => $idPrice,
                    'depositPrice' => $depositPrice,
                    'payPerUse' => $payPerUse,
                    'totalPrice' => $totalPrice,
                ];

                return $price;

            //毎月請求
            case self::TYPE_MONTHLY:
                if(strtotime($traialMonth) === strtotime($claimMonth)){
                    $trialPrice = $trialUnitPrice * $trialSearchCount;

                }elseif(strtotime($startMonth) === strtotime($claimMonth)){
                    $idPrice = $idUnitPrice * $detail['ids'];
                    $payPerUse = $trialUnitPrice * $trialSearchCount + $searchUnitPrice * $actuallySearchCount;

                }else{
                    $idPrice = $idUnitPrice * $detail['ids'];
                    $payPerUse = $searchUnitPrice * $actuallySearchCount;
                }

                $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;
                $price = [
                    'trialPrice'=> $trialPrice,
                    'idPrice' => $idPrice,
                    'depositPrice' => $depositPrice,
                    'payPerUse' => $payPerUse,
                    'totalPrice' => $totalPrice,
                ];

                return $price;
        }
    }

     /**
     * 前月の請求有無
     *
     * @param $companyId
     * @param $date
     * @return $claim
     */
    public function getClaimStatus($companyId, $date){
        $date = new DateTime($date);
        $prevDate = $date->modify("-1 month");
        $prevMonth = $prevDate->format('Ym');

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $prevMonth);
        $query->where('claimStatus', self::CLAIM_STATUS_DONE);
        $count = $query->first();

        if($count->count >= 1){
            $claim = true;
        }else{
            $claim = false;
        }
        
        return $claim;
    }
}
