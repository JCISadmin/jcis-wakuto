<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Datetime;

class TClaim extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tClaim';

    const TYPE_ALL_DEPOSIT = 'allDepo';
    const TYPE_ID_DEPOSIT = 'idDepo';
    const TYPE_MONTHLY = 'allMonth';

    const DATE_LOW_VALUE = '2000-01-01';
    const DATE_HIGH_VALUE = '3000-01-01';

    /** 契約情報 @var array|null */
    protected ?array $contractInfo;

    /** ID単価 @var integer */
    private int $idUnitPrice;

    /** 検索単価 @var integer */
    private int $searchUnitPrice;

    /** 年間検索数 @var integer */
    private int $yearSearchCount;

    /** デポジット残高 @var integer */
    private int $deposit;

    /** トライアル検索単価 @var integer  */
    private int $trialUnitPrice;

    /** トライアル検索数 @var integer  */
    private int $trialSearchCount;

    /** 月間検索数 @var integer  */
    private int $searchCount;

    /** 課金検索数 @var integer  */
    private int $chargeSearchCount;

    /**
     * 請求情報を取得
     *
     * @param $claimMonth
     * @param $companyName
     * @param $companyIds
     * @param $pageLine
     * @param bool $paginateFlg
     * @param bool $useClaimStatus
     * @return LengthAwarePaginator|Collection $list
     * @throws Exception
     */
    public function getList($claimMonth, $companyName, $companyIds, $pageLine, bool $paginateFlg, bool $useClaimStatus): LengthAwarePaginator|Collection
    {
        //モデルインスタンスを作成
        $keywordHistoryModel = new TKeywordHistory();
        $vatModel = new MVat();
        $contractPlanModel = new TContractPlan();
        $tClaimDetailModel = new TClaimDetail();

        $year = date_format(new DateTime($claimMonth), 'Y');
        $month = date_format(new DateTime($claimMonth), 'm');
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $startMonth = new DateTime($claimMonth);
        $startMonth->modify('first day of this month');
        $endMonth = new DateTime($claimMonth);
        $endMonth->modify('last day of this month');

        $idNum = DB::table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->groupBy(['companyId', 'contractPlanId']);

        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'webPlanIds.ids',
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->leftjoinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->where('mContractPlan.planType', 'web');

        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'apiPlanIds.ids',
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->leftjoinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->where('mContractPlan.planType', 'api');

        //期間が一致するレコードのseqNo取得(web)
        $webSeqNo = DB::table('tContractPlanDetail');
        $webSeqNo->select(
            'tContractPlanDetail.*',
        );
        $webSeqNo->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webSeqNo->where('mContractPlan.planType', 'web');
        $webSeqNo->whereDate('tContractPlanDetail.contractStartDate', '<=', $endMonth);
        $webSeqNo->whereDate('tContractPlanDetail.contractEndDate', '>=', $startMonth);
        $webSeqNo->groupBy('tContractPlanDetail.companyId');

        $webPlanDetail = DB::table('tContractPlanDetail');
        $webPlanDetail->select(
            'tContractPlanDetail.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'webPlanIds.ids',
            'webSeqNo.seqNo as webSeqNo',
        );
        $webPlanDetail->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlanDetail->join('mContractType', function ($join) {
            $join->on('tContractPlanDetail.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $webPlanDetail->leftjoinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlanDetail->joinSub($webSeqNo, 'webSeqNo', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'webSeqNo.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'webSeqNo.contractPlanId');
            $join->on('tContractPlanDetail.seqNo', '=', 'webSeqNo.seqNo');
        });
        $webPlanDetail->where('mContractPlan.planType', 'web');

        //期間が一致するレコードのseqNo取得(api)
        $apiSeqNo = DB::table('tContractPlanDetail');
        $apiSeqNo->select(
            'tContractPlanDetail.*',
        );
        $apiSeqNo->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiSeqNo->where('mContractPlan.planType', 'api');
        $apiSeqNo->whereDate('tContractPlanDetail.contractStartDate', '<=', $endMonth);
        $apiSeqNo->whereDate('tContractPlanDetail.contractEndDate', '>=', $startMonth);
        $apiSeqNo->groupBy('tContractPlanDetail.companyId');

        $apiPlanDetail = DB::table('tContractPlanDetail');
        $apiPlanDetail->select(
            'tContractPlanDetail.*',
            'mContractPlan.planType',
            'mContractPlan.name as contractPlanName',
            'mContractType.name as contractTypeName',
            'apiPlanIds.ids',
            'apiSeqNo.seqNo as apiSeqNo',
        );
        $apiPlanDetail->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlanDetail->join('mContractType', function ($join) {
            $join->on('tContractPlanDetail.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $apiPlanDetail->leftjoinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlanDetail->joinSub($apiSeqNo, 'apiSeqNo', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'apiSeqNo.companyId');
            $join->on('tContractPlanDetail.contractPlanId', '=', 'apiSeqNo.contractPlanId');
            $join->on('tContractPlanDetail.seqNo', '=', 'apiSeqNo.seqNo');
        });
        $apiPlanDetail->where('mContractPlan.planType', 'api');

        $claim = DB::table('tClaim');
        $claim->select(
            'tClaim.*',
        );
        $claim->where('claimMonth', $strClaimMonth);

        $user = DB::table('mUserCompany');
        $user->select(
            'mUserCompany.*',
            'webPlan.companyId as webCompanyId',
            'webPlanDetail.companyId as webDetailCompanyId',
            'webPlan.contractPlanId as webContractPlanId',
            'webPlan.contractPlanName as webContractPlanName',
            'webPlanDetail.contractPlanId as webDetailContractPlanId',
            'webPlanDetail.contractPlanName as webDetailContractPlanName',
            'webPlanDetail.contractTypeId as webDetailContractTypeId',
            'webPlanDetail.contractTypeName as webDetailContractTypeName',
            'webPlan.planType as webPlanType',
            'webPlanDetail.planType as webDetailPlanType',
            'webPlanDetail.idUnitPrice as webDetailIdUnitPrice',
            'webPlanDetail.searchUnitPrice as webDetailSearchUnitPrice',
            'webPlanDetail.searchCount as webDetailSearchCount',
            'webPlan.deposit as webDeposit',
            'webPlanDetail.webSeqNo as webDetailSeqNo',
            'apiPlan.companyId as apiCompanyId',
            'apiPlanDetail.companyId as apiDetailCompanyId',
            'apiPlan.contractPlanId as apiContractPlanId',
            'apiPlan.contractPlanName as apiContractPlanName',
            'apiPlanDetail.contractPlanId as apiDetailContractPlanId',
            'apiPlanDetail.contractPlanName as apiDetailContractPlanName',
            'apiPlanDetail.contractTypeId as apiDetailContractTypeId',
            'apiPlanDetail.contractTypeName as apiDetailContractTypeName',
            'apiPlan.planType as apiPlanType',
            'apiPlanDetail.planType as apiDetailPlanType',
            'apiPlanDetail.idUnitPrice as apiDetailIdUnitPrice',
            'apiPlanDetail.searchUnitPrice as apiDetailSearchUnitPrice',
            'apiPlanDetail.searchCount as apiDetailSearchCount',
            'apiPlan.deposit as apiDeposit',
            'apiPlanDetail.apiSeqNo as apiDetailSeqNo',
            'mContractStatus.name as statusName',
            'claim.claimNo',
            'claim.price',
            'claim.claimNote',
            'claim.claimStatus',
            'claim.paymentStatus',
            'claim.claimDate',
            'claim.paymentDate',
            'claim.memo as claimMemo',
        );
        $user->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });

        $user->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });

        $user->leftJoinSub($webPlanDetail, 'webPlanDetail', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlanDetail.companyId');
        });

        $user->leftJoinSub($apiPlanDetail, 'apiPlanDetail', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlanDetail.companyId');
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
            $query->where('name', 'like', '%' . $companyName . '%');
        }

        if(is_null($companyIds) === false){
            $idAry = [];
            foreach($companyIds as $id){
                $idAry[] = $id;
            }
            $query->whereIn('companyId', $idAry);
        }

        if($paginateFlg === true){
            if ($pageLine == '') {
                $pageLine = self::PAGE_LINE;
            }
            $list = $query->paginate($pageLine);
        }elseif($useClaimStatus === true){
            $query->where('claimStatus', self::CLAIM_STATUS_DONE);
            $list = $query->get();
        }else{
            $list = $query->get();
        }

        //税率を取得
        $claimDate = date('Y-m-d', strtotime('last day of' . $claimMonth));
        $tax = $vatModel->getTax($claimDate);

        foreach($list as $key => $items){
            //有効なID数を取得
            $webContractPlan = $contractPlanModel->getPlan($items->webCompanyId, self::PLAN_TYPE_WEB);
            if(is_null($webContractPlan)){
                $list[$key]->webIds = 0;
            }else{
                $list[$key]->webIds = $webContractPlan['ids'];
            }

            $apiContractPlan = $contractPlanModel->getPlan($items->apiCompanyId, self::PLAN_TYPE_API);
            if(is_null($apiContractPlan)){
                $list[$key]->apiIds = 0;
            }else{
                $list[$key]->apiIds = $apiContractPlan['ids'];
            }

            //月間検索数を取得
            $list[$key]->webMonthSearchCount = $keywordHistoryModel->getMonthSearchCount($items->webCompanyId, null, $items->webContractPlanId, $year, $month);
            $list[$key]->apiMonthSearchCount = $keywordHistoryModel->getMonthSearchCount($items->apiCompanyId, null, $items->apiContractPlanId, $year, $month);

            //WEB検索契約の請求額を取得
            $webDeposit = is_null($items->webDeposit) ? 0 : $items->webDeposit;
            $this->deposit = $webDeposit;
            $webPrice = $this->getPrice($claimMonth, $items, $items->webPlanType, self::PLAN_TYPE_WEB);

            //API検索契約の請求額を取得
            $apiDeposit = is_null($items->apiDeposit) ? 0 : $items->apiDeposit;
            $this->deposit = $apiDeposit;
            $apiPrice = $this->getPrice($claimMonth, $items, $items->apiPlanType, self::PLAN_TYPE_API);

            $list[$key]->items = [
                'web' => $webPrice,
                'api' => $apiPrice,
            ];


            //請求額(補正額抜き・税抜き)
            if(is_null($list[$key]->claimStatus)){
                //請求データ無
                $price = $webPrice['totalPrice'] + $apiPrice['totalPrice'];
            }else{
                //請求データ有
                $price = is_null($items->price) ? 0 : $items->price;
            }
            $list[$key]->price = $price;

            //補正金額
            $adjustPrice = $tClaimDetailModel->getAdjustPrice($items->companyId, $strClaimMonth);
            $list[$key]->adjustPrice = $adjustPrice;

            //請求額（補正額込み・税抜き）
            $priceWithoutTax = $price + $adjustPrice;
            $list[$key]->priceWithoutTax = $priceWithoutTax;

            //税率
            $list[$key]->tax = $tax;

            //税額
            $taxPrice = round(($priceWithoutTax) * $tax / 100);
            $list[$key]->taxPrice = $taxPrice;

            //税込額
            $list[$key]->priceWithTax = $priceWithoutTax + $taxPrice;
        }

        return $list;
    }

    /**
     * 請求ステータスを請求済に変更
     *
     * @param $companyId
     * @param $claimMonth
     * @throws Exception
     */
    public function changeClaimStatus($companyId, $claimMonth)
    {
        $tClaimDetailModel = new TClaimDetail();
        $companyIds[] = $companyId;
        $claimData = $this->getList($claimMonth, null, $companyIds, null, false, false);
        $calcPrice = $tClaimDetailModel->getCalcPrice($companyId, $claimMonth);

        //前払いステータス
        $webPrepaidStatus = $this->getPrepaidStatusValue($claimData, self::PLAN_TYPE_WEB);
        $apiPrepaidStatus = $this->getPrepaidStatusValue($claimData, self::PLAN_TYPE_API);

        $dt = new Datetime();
        $now = $dt->format('Ymd');
        //発行日（請求月末）
        $claimDate = date('Y-m-d');
        //支払日（請求翌月末）
        $paymentDate = date('Y-m-d', strtotime('last day of next month' . $claimMonth));
        //請求月（YYYYMM）
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $count = $query->first();

        $lockName = 'claimLock';
        $timeOut = 300;

        $this->begin();

        try{
            $lock = DB::select('select get_lock(?, ?) as result', [$lockName, $timeOut]);
            if ($lock[0]->result === 1) {
                //ロック取得成功

                if ($count->count > 0) {
                    //既存データあり
                    $upd = DB::table($this->table);
                    $upd->where('companyId', $companyId);
                    $upd->where('claimMonth', $strClaimMonth);
                    $upd->update([
                        'claimStatus' => self::CLAIM_STATUS_DONE,
                        'updateDatetime' => $now,
                    ]);

                } else {
                    //既存データなし
                    $ins = DB::table($this->table);
                    $ins->insert([
                        'companyId' => $companyId,
                        'claimMonth' => $strClaimMonth,
                        'claimNo' => $this->getClaimNo(),
                        'price' => $calcPrice,
                        'claimDate' => $claimDate,
                        'paymentDate' => $paymentDate,
                        'claimStatus' => self::CLAIM_STATUS_DONE,
                        'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                        'createDatetime' => $now,
                        'updateDatetime' => $now,
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,
                    ]);
                }
            }
        } finally {
            DB::select('select release_lock(?)', [$lockName]);

        }

        $this->commit();
    }

    /**
     * 入金ステータスを入金済に変更
     *
     * @param $companyId
     * @param $claimMonth
     * @throws Exception
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
     * @return array $price
     * @throws Exception
     */
    public function getPrice($claimMonth, $data, $planType): array
    {
        // モデルインスタンスの取得
        $mContractPlanModel = new MContractPlan();
        $keywordHistoryModel = new TKeywordHistory();
        $tContractPlanModel = new TContractPlan();

        // 契約情報
        $this->contractInfo = $tContractPlanModel->getPlan($data->companyId, $planType);
        if(is_null($this->contractInfo)){
            return [
                'trial' => [
                    'amount' => 0,
                    'unitPrice' => 0,
                    'price' => 0,
                ],
                'id' => [
                    'amount' => 0,
                    'unitPrice' => 0,
                    'price' => 0,
                ],
                'deposit' =>[
                    'amount' => 0,
                    'unitPrice' => 0,
                    'price' => 0,
                ],
                'payPerUse' =>[
                    'amount' => 0,
                    'unitPrice' => 0,
                    'price' => 0,
                ],
                'totalPrice' => 0
            ];
        }

        // 契約情報の補正
        // ID単価
        $this->idUnitPrice = is_null($this->contractInfo['contractDetail']['idUnitPrice']) ? 0 : $this->contractInfo['contractDetail']['idUnitPrice'];

        // 検索単価
        $this->searchUnitPrice = is_null($this->contractInfo['contractDetail']['searchUnitPrice']) ? 0 : $this->contractInfo['contractDetail']['searchUnitPrice'];

        // 年間検索数
        $this->yearSearchCount = is_null($this->contractInfo['contractDetail']['searchCount']) ? 0 : $this->contractInfo['contractDetail']['searchCount'];

        // 請求日付情報取得
        $dateInfo = $this->getClaimDateInfo($claimMonth);

        // トライアル関連
        $this->trialUnitPrice = 0;
        $this->trialSearchCount = 0;
        $trialPlanId = config('hds.contract.trialPlan.'.$planType);
        if($trialPlanId !== '') {
            $planInfo = $mContractPlanModel->get($trialPlanId);
            $this->trialUnitPrice = $planInfo->unitPrice;
            // トライアル検索数取得
            $this->trialSearchCount = $keywordHistoryModel->getSearchCount($data->companyId, $this->contractInfo['contractDetail']['contractPlanId'], null, date_format(new DateTime($dateInfo['startTrial']), 'Y-m-d 0:00:00'), date_format(new DateTime($dateInfo['endTrial']), 'Y-m-d 23:59:59'));
        }

        // 検索数取得
        $this->searchCount = $keywordHistoryModel->getSearchCount($data->companyId, $this->contractInfo['contractDetail']['contractPlanId'], null, date_format(new DateTime($dateInfo['startUse']), 'Y-m-d 0:00:00'), date_format(new DateTime($dateInfo['endUse']), 'Y-m-d 23:59:59'));

        // 課金対象の検索数取得
        $this->chargeSearchCount = $keywordHistoryModel->getChargeSearchCount($data->companyId, $this->contractInfo['contractDetail']['contractPlanId'], date_format(new DateTime($dateInfo['startUse']), 'Y-m-d 0:00:00'), date_format(new DateTime($dateInfo['endUse']), 'Y-m-d 23:59:59'));

        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
        switch ($this->contractInfo['contractDetail']['contractTypeId']) {
            case self::TYPE_ALL_DEPOSIT:
                $ret = $this->calcAllDeposit($data, $dateInfo, $planType);
                break;

            case self::TYPE_ID_DEPOSIT:
                $ret = $this->calcIdDeposit($data, $dateInfo, $planType);
                break;

            case self::TYPE_MONTHLY:
                $ret = $this->calcMonthly($dateInfo);
                break;

            default:
                $ret = [
                    'trial' => [
                        'amount' => 0,
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
                    'id' => [
                        'amount' => 0,
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
                    'deposit' =>[
                        'amount' => 0,
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
                    'payPerUse' =>[
                        'amount' => 0,
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
                    'totalPrice' => 0,
                ];

        }

        return $ret;

    }

    /**
     * 全額デポジット計算
     *
     * @param $data
     * @param $dateInfo
     * @param $planType
     * @return array
     * @throws Exception
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcAllDeposit($data, $dateInfo, $planType): array
    {

        // トライル料金
        $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

        $idPrice = 0;
        $depositPrice = 0;
        //全額デポジットの場合、ID代単価を1年分とする
        $this->idUnitPrice *= 12;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
            // 請求月翌月が契約更新月の時
            $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
            $depositPrice = $this->searchUnitPrice * $this->yearSearchCount;
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateMonth']) {
            if ($this->getPrepaidStatus($data->companyId, $planType, $dateInfo['updateBeforeMonth']) === false) {
                $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
                $depositPrice = $this->searchUnitPrice * $this->yearSearchCount;
            }
        }

        //課金額
        $overageCharges = $this->searchUnitPrice * $this->chargeSearchCount;

        // デポジット不足
        if($this->deposit == 0){
            //デポジット残高が0の場合、課金額をデポジット不足として請求
            $chargeSearchCount = $this->chargeSearchCount;
            $payPerUse = $overageCharges;
        }else{
            $chargeSearchCount = 0;
            $payPerUse = 0;
        }

        $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;

        return [
            'trial' => [
                'amount' => $this->trialSearchCount,
                'unitPrice' => $this->trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $this->contractInfo['ids'],
                'unitPrice' => $this->idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => $this->yearSearchCount,
                'unitPrice' => $this->searchUnitPrice,
                'price' => $depositPrice,
            ],
            'payPerUse' =>[
                'amount' => $chargeSearchCount,
                'unitPrice' => $this->searchUnitPrice,
                'price' => $payPerUse,
                'overageCharges' => $overageCharges,
            ],
            'totalPrice' => $totalPrice,
        ];

    }

    /**
     * ID台のみディポジット計算
     *
     * @param $data
     * @param $dateInfo
     * @param $planType
     * @return array
     * @throws Exception
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcIdDeposit($data, $dateInfo, $planType): array
    {
        // トライル料金
        $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

        $idPrice = 0;
        //ID代のみデポジットの場合、ID代単価を1年分とする
        $this->idUnitPrice *= 12;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
            $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateMonth']) {
            if ($this->getPrepaidStatus($data->companyId, $planType, $dateInfo['updateBeforeMonth']) === false) {
                $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
            }
        }

        $payPerUse = $this->searchUnitPrice * $this->searchCount;
        $totalPrice = $trialPrice + $payPerUse + $idPrice;

        return [
            'trial' => [
                'amount' => $this->trialSearchCount,
                'unitPrice' => $this->trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $this->contractInfo['ids'],
                'unitPrice' => $this->idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => 0,
                'unitPrice' => 0,
                'price' => 0,
            ],
            'payPerUse' =>[
                'amount' => $this->searchCount,
                'unitPrice' => $this->searchUnitPrice,
                'price' => $payPerUse,
            ],
            'totalPrice' => $totalPrice,
        ];

    }

    /**
     * 毎月請求の計算
     *
     * @param $dateInfo
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcMonthly($dateInfo): array
    {
        $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

        $idPrice = 0;
        if(is_null($dateInfo['startMonth']) === false){
            if($dateInfo['claimMonth'] >= $dateInfo['startMonth'] && $dateInfo['claimMonth'] <= $dateInfo['endMonth']){
                $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
            }
        }

        $payPerUse = $this->searchUnitPrice * $this->searchCount;

        $totalPrice = $trialPrice + $payPerUse + $idPrice;

        return [
            'trial' => [
                'amount' => $this->trialSearchCount,
                'unitPrice' => $this->trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $this->contractInfo['ids'],
                'unitPrice' => $this->idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => 0,
                'unitPrice' => 0,
                'price' => 0,
            ],
            'payPerUse' =>[
                'amount' => $this->searchCount,
                'unitPrice' => $this->searchUnitPrice,
                'price' => $payPerUse,
            ],
            'totalPrice' => $totalPrice,
        ];

    }

    /**
     * 請求関連日付の生成
     *
     * @param $claimMonth
     * @return array
     * @throws Exception
     */
    private function getClaimDateInfo($claimMonth): array
    {
        $dateInfo = [];
        $dateInfo['claimMonth'] = $claimMonth;

        $dtClaimMonth = new Datetime($claimMonth . '-01');

        // 請求月月初日
        $dateInfo['startDate'] = $claimMonth . '-01';

        // 請求月月末日
        $dt = clone $dtClaimMonth;
        $dateInfo['endDate'] = $dt->modify('last day of this month')->format('Y-m-d');

        // 請求前月
        $dt = clone $dtClaimMonth;
        $dateInfo['prevMonth'] = $dt->modify('first day of last month')->format('Y-m');
        $dateInfo['prevMonthStartDate'] = $dateInfo['prevMonth'] . '-01';
        $dateInfo['prevMonthEndDate'] = $dt->modify('last day of this month')->format('Y-m-d');

        // 請求翌月
        $dt = clone $dtClaimMonth;
        $dateInfo['nextMonth'] = $dt->modify('first day of next month')->format('Y-m');

        // トライアル日付
        $dateInfo['startTrial'] = self::DATE_HIGH_VALUE;
        $dateInfo['endTrial'] = self::DATE_LOW_VALUE;
        $incollectOrderFlg = false;

        if (is_null($this->contractInfo['startTrial']) === false) {
            //　請求月のトライアル期間（開始）
            if ($this->contractInfo['startTrial'] < $dateInfo['startDate']) {
                //　トライアル開始日が請求月より前の日付の場合　：　請求月初日
                $dateInfo['startTrial'] = $dateInfo['startDate'];

            } elseif ($dateInfo['startDate'] <= $this->contractInfo['startTrial'] && $this->contractInfo['startTrial'] <= $dateInfo['endDate']) {
                // トライアル開始日が請求月中の日付の場合　：　トライアル開始日
                $dateInfo['startTrial'] = $this->contractInfo['startTrial'];

            }

            // 請求月のトライアル期間（終了）
            if (is_null($this->contractInfo['useStartDate'])) {
                //　利用開始日が未設定の場合　：　請求月末日
                $dtUseStart = new Datetime($dateInfo['endDate']);
                $dateInfo['endTrial'] = $dtUseStart->format('Y-m-d');

            } else {
                if ($this->contractInfo['startTrial'] < $this->contractInfo['useStartDate']) {
                    //　利用開始日が、トライアル開始日より後の日付の場合

                    if ($dateInfo['endDate'] < $this->contractInfo['useStartDate']) {
                        //　利用開始日が請求月より後の場合　：　請求月末日
                        $dtUseStart = new Datetime($dateInfo['endDate']); 
                        $dateInfo['endTrial'] = $dtUseStart->format('Y-m-d');
                    
                    } elseif ($dateInfo['startDate'] <= $this->contractInfo['useStartDate'] && $this->contractInfo['useStartDate'] <= $dateInfo['endDate']) {
                        //　利用開始日が請求月中の場合　：　利用開始日前日
                        $dtUseStart = new Datetime($this->contractInfo['useStartDate']);
                        $dateInfo['endTrial'] = $dtUseStart->modify('-1 day')->format('Y-m-d');

                    }

                } elseif ($this->contractInfo['startTrial'] > $this->contractInfo['useStartDate']) {
                    //　トライアル開始日と利用開始日の時系列が逆転している場合
                    $incollectOrderFlg = true;                    

                }
            }

        }

        // 利用日付
        $dateInfo['startUse'] = self::DATE_HIGH_VALUE;
        $dateInfo['endUse'] = self::DATE_LOW_VALUE;
        $dateInfo['startBeforeMonth'] = substr(self::DATE_LOW_VALUE, 0, 7);
        $dateInfo['endMonth'] = substr($dateInfo['endDate'], 0, 7);
        $dateInfo['updateMonth'] = null;
        $dateInfo['updateBeforeMonth'] = null;
        $dateInfo['startMonth'] = null;

        if (is_null($this->contractInfo['useStartDate']) === false) {
            // 請求月の本契約期間（開始・終了）
            if($incollectOrderFlg === false){
                //トライアル開始日と利用開始日の時系列が逆転していない場合

                if ($dateInfo['startDate'] <= $this->contractInfo['useStartDate'] && $this->contractInfo['useStartDate'] <= $dateInfo['endDate']) {
                    // 利用開始日が請求月中の日付の場合　：　　開始＝利用開始日　終了＝請求月末日
                    $dateInfo['startUse'] = $this->contractInfo['useStartDate'];
                    $dateInfo['endUse'] = $dateInfo['endDate'];

                }elseif ($this->contractInfo['useStartDate'] < $dateInfo['startDate']){
                    //　利用開始日が請求月よりも前の日付の場合　：　開始＝請求月初日　終了＝請求月末日
                    $dateInfo['startUse'] = $dateInfo['startDate'];
                    $dateInfo['endUse'] = $dateInfo['endDate'];

                }
            }

            // 契約更新日
            if (is_null($this->contractInfo['useUpdateDate'])) {
                // 契約更新日が未設定の場合
                $dateInfo['updateMonth'] = substr($this->contractInfo['useStartDate'], 0, 7);
            }else{
                $dateInfo['updateMonth'] = substr($this->contractInfo['useUpdateDate'], 0, 7);
            }

            // 契約更新前月
            $dateInfo['updateBeforeMonth'] = (new Datetime($dateInfo['updateMonth']))->modify('-1 month')->format('Y-m');

            //利用開始月
            $dateInfo['startMonth'] = substr($this->contractInfo['useStartDate'], 0, 7);
        }

        return $dateInfo;
    }

    /**
     * 指定月の請求有無
     *
     * @param $companyId
     * @param $date
     * @return bool $claim
     * @throws Exception
     */
    public function getClaimStatus($companyId, $date): bool
    {
        $date = new DateTime($date);
        $claimMonth = $date->format('Ym');

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $query->where('claimStatus', self::CLAIM_STATUS_DONE);
        $count = $query->first();

        if($count->count >= 1){
            $claim = true;
        }else{
            $claim = false;
        }

        return $claim;
    }

    /**
     * 更新前月の前払いの請求状況
     *
     * @param $companyId
     * @param $planType
     * @param $updateBeforeMonth
     * @return bool $done
     * @throws Exception
     */
    public function getPrepaidStatus($companyId, $planType, $updateBeforeMonth): bool
    {
        $month = ( new DateTime( $updateBeforeMonth ) )->format('Ym');
        //更新前月が請求済 AND 初期費用請求済
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $month);
        $query->where('claimStatus', self::CLAIM_STATUS_DONE);
        if($planType === self::PLAN_TYPE_WEB){
            $query->where('webPrepaidStatus', self::PREPAID_DONE);
        }elseif($planType === self::PLAN_TYPE_API){
            $query->where('apiPrepaidStatus', self::PREPAID_DONE);
        }
        $count = $query->first();

        if($count->count == 0){
            //更新前月に請求データなし OR 請求未済 OR 初期費用未請求
            $done = false;
        }else{
            $done = true;
        }

        return $done;
    }

    /**
     * 請求番号(YYYYMMDDNNN)を生成
     *
     * @return int|string $claimNo
     */
    public function getClaimNo(): int|string
    {
        $dt = new Datetime();
        $now = $dt->format('Ymd');

        $query = DB::table($this->table);
        $query->select(DB::raw('MAX(claimNo) as maxClaimNo'));
        $query->where('claimNo', 'like', "$now"."___");
        $max = $query->first();

        if(is_null($max->maxClaimNo)){
            $claimNo = $now.'001';
        }else{
            $maxClaimNo = $max->maxClaimNo;
            $claimNo = (int)$maxClaimNo + 1;
        }

        return $claimNo;
    }

    /**
     * 更新
     *
     * @param $companyId
     * @param $claimMonth
     * @param $updateData
     * @throws Exception
     */
    public function claimUpdate($companyId, $claimMonth, $updateData)
    {
        $dt = new Datetime();
        $now = $dt->format('Ymd');
        //請求月
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $lockName = 'claimLock';
        $timeOut = 300;

        //既存データの数をカウント
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $count = $query->first();

        //更新値で計算した請求額を取得
        $tClaimDetailModel = new TClaimDetail();
        $companyIds[] = $companyId;
        $claimList = $this->getList($claimMonth, null, $companyIds, null, false, false);
        $calcPrice = $tClaimDetailModel->getCalcPrice($companyId, $claimMonth);

        //前払いステータス
        $webPrepaidStatus = $this->getPrepaidStatusValue($claimList, self::PLAN_TYPE_WEB);
        $apiPrepaidStatus = $this->getPrepaidStatusValue($claimList, self::PLAN_TYPE_API);

        $this->begin();

        //WEBプラン
        if(is_null($claimList[0]->webContractPlanId) === false){
            //契約データあり
            $updContract = DB::table('tContractPlan');
            $updContract->where('companyId', $companyId);
            $updContract->where('contractPlanId', $claimList[0]->webContractPlanId);
            $updContract->update([
                'deposit' => $updateData['webDeposit'],
                'updateDatetime' => $now,
            ]);
        }

        //APIプラン
        if(is_null($claimList[0]->apiContractPlanId) === false){
            //契約データあり
            $updContract = DB::table('tContractPlan');
            $updContract->where('companyId', $companyId);
            $updContract->where('contractPlanId', $claimList[0]->apiContractPlanId);
            $updContract->update([
                'deposit' => $updateData['apiDeposit'],
                'updateDatetime' => $now,
            ]);
        }

        try{
            $lock = DB::select('select get_lock(?, ?) as result', [$lockName, $timeOut]);
            if ($lock[0]->result === 1) {
                //ロック取得成功

                if($count->count > 0){
                    //既存データあり
                    $upd = DB::table($this->table);
                    $upd->where('companyId', $companyId);
                    $upd->where('claimMonth', $strClaimMonth);
                    $upd->update([
                        'price' => $calcPrice,
                        'claimDate' => $updateData['claimDate'],
                        'paymentDate' => $updateData['paymentDate'],
                        'claimNote' => $updateData['claimNote'],
                        'memo' => $updateData['memo'],
                        'updateDatetime' => $now,
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,
                    ]);

                }else{
                    //既存データなし
                    $ins = DB::table($this->table);
                    $ins->insert([
                        'companyId' => $companyId,
                        'claimMonth' => $strClaimMonth,
                        'claimDate' => $updateData['claimDate'],
                        'claimNo' => $this->getClaimNo(),
                        'price' => $calcPrice,
                        'claimStatus' => self::CLAIM_STATUS_UNDONE,
                        'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                        'paymentDate' => $updateData['paymentDate'],
                        'claimNote' => $updateData['claimNote'],
                        'memo' => $updateData['memo'],
                        'createDatetime' => $now,
                        'updateDatetime' => $now,
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,

                    ]);
                }
            }
        } finally {
            DB::select('select release_lock(?)', [$lockName]);

        }

        $this->commit();
    }

    /**
     * 請求額を取得
     *
     * @param $claimMonth
     * @param $claimList
     * @param $webDeposit
     * @param $apiDeposit
     * @return mixed $calcPrice
     * @throws Exception
     */
    public function getCalcPrice($claimMonth, $claimList, $webDeposit, $apiDeposit): mixed
    {
        //WEB検索契約の請求額を取得
        $this->deposit = is_null($webDeposit) ? 0 : $webDeposit;
        $webPrice = $this->getPrice($claimMonth, $claimList, $claimList->webPlanType, self::PLAN_TYPE_WEB);

        //API検索契約の請求額を取得
        $this->deposit = is_null($apiDeposit) ? 0 : $apiDeposit;
        $apiPrice = $this->getPrice($claimMonth, $claimList, $claimList->apiPlanType, self::PLAN_TYPE_API);

        //請求額(補正額抜き・税抜き)
        $webTotalPrice = $webPrice['totalPrice'];
        $apiTotalPrice = $apiPrice['totalPrice'];

        return $webTotalPrice + $apiTotalPrice;
    }

    /**
     * 前払いステータスとして登録する値を取得
     *
     * @param $claimData
     * @param $planType
     * @return $prepaidStatus
     */
    public function getPrepaidStatusValue($claimData, $planType)
    {
        switch ($planType) {
            case self::PLAN_TYPE_WEB:
                $prepaidStatus = self::PREPAID_UNDONE;
                if(is_null($claimData[0]->items['web']) === false){
                    $prepaidCharge = 0;
                    if($claimData[0]->webContractTypeId === self::TYPE_ALL_DEPOSIT){
                        $prepaidCharge = $claimData[0]->items['web']['id']['price'] + $claimData[0]->items['web']['deposit']['price'];
                    }elseif($claimData[0]->webContractTypeId === self::TYPE_ID_DEPOSIT){
                        $prepaidCharge = $claimData[0]->items['web']['id']['price'];
                    }
        
                    if($prepaidCharge > 0){
                        $prepaidStatus = self::PREPAID_DONE;
                    }
                }
                break;

            case self::PLAN_TYPE_API:
                $prepaidStatus = self::PREPAID_UNDONE;
                if(is_null($claimData[0]->items['api']) === false){
                    $prepaidCharge = 0;
                    if($claimData[0]->webContractTypeId === self::TYPE_ALL_DEPOSIT){
                        $prepaidCharge = $claimData[0]->items['api']['id']['price'] + $claimData[0]->items['api']['deposit']['price'];
                    }elseif($claimData[0]->webContractTypeId === self::TYPE_ID_DEPOSIT){
                        $prepaidCharge = $claimData[0]->items['api']['id']['price'];
                    }
        
                    if($prepaidCharge > 0){
                        $prepaidStatus = self::PREPAID_DONE;
                    }
                }
                break;

            default:
                $prepaidStatus = self::PREPAID_UNDONE;
        }

        return $prepaidStatus;
    }

    /**
     * 請求データの有無をチェック
     *
     * @param $companyId
     * @param $claimMonth
     * @return $count
     */
    public function countClaimData($companyId, $claimMonth)
    {
        //請求月
        $strClaimMonth = str_replace('-', '', $claimMonth);

        //既存データの数をカウント
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
      
        $count = $query->first();
    
        return $count->count;
    }
}
