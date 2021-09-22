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

    const TYPE_ALL_DEPOSIT = 1;
    const TYPE_ID_DEPOSIT = 2;
    const TYPE_MONTHLY = 3;

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
            'webPlan.idUnitPrice as webPlanIdUnitPrice',
            'webPlan.searchUnitPrice as webPlanSearchUnitPrice',
            'webPlan.searchCount as webPlanSearchCount',
            'webPlan.deposit as webPlanDeposit',
            'webPlan.ids as webPlanIds',
            'apiPlan.companyId as apiPlanCompanyId',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.contractPlanName as apiPlanPlanName',
            'apiPlan.contractTypeId as apiPlanTypeId',
            'apiPlan.contractTypeName as apiPlanTypeName',
            'apiPlan.planType as apiPlanPlanType',
            'apiPlan.idUnitPrice as apiPlanIdUnitPrice',
            'apiPlan.searchUnitPrice as apiPlanSearchUnitPrice',
            'apiPlan.searchCount as apiPlanSearchCount',
            'apiPlan.deposit as apiPlanDeposit',
            'apiPlan.ids as apiPlanIds',
            'mContractStatus.name as statusName',
            'claim.claimNo',
            'claim.price',
			'claim.adjustNote',
            'claim.adjustPrice',
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

        $keywordHistoryModel = new TKeywordHistory();
        $vatModel = new MVat();
        $claimDate = date('Y-m-d', strtotime('last day of' . $claimMonth));
        $tax = $vatModel->getTax($claimDate);

        foreach($list as $key => $items){
            $list[$key]->webPlanMonthSearchCount = $keywordHistoryModel->getMonthSearchCount($items->webPlanCompanyId, null, $items->webPlanPlanId, $year, $month);
            $list[$key]->apiPlanMonthSearchCount = $keywordHistoryModel->getMonthSearchCount($items->apiPlanCompanyId, null, $items->apiPlanPlanId, $year, $month);

            $list[$key]->tax = $tax;
            $adjustPrice = $items->adjustPrice === null ? 0 : $items->adjustPrice;
            $webPrice = $this->getPrice($claimMonth, $items, $items->webPlanPlanType);
            $apiPrice = $this->getPrice($claimMonth, $items, $items->apiPlanPlanType);
            $list[$key]->items = [
                'web' => $webPrice,
                'api' => $apiPrice,
            ];

            if($list[$key]->claimStatus === self::PAYMENT_STATUS_DONE){
                //請求済の場合
                $price = $items->price === null ? 0 : $items->price;
            }else{
                //請求未済の場合
                $price = $webPrice['totalPrice'] + $apiPrice['totalPrice'];
            }

            $taxPrice = round(($price + $adjustPrice) * $tax / 100);
            $list[$key]->price = $price;
            $list[$key]->adjustPrice = $adjustPrice;
            $list[$key]->taxPrice = $taxPrice;
            $list[$key]->priceWithTax = $price + $adjustPrice + $taxPrice;
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
        $price = 0;

        $dt = new Datetime();
        $now = $dt->format('Ymd');
        $claimDate = date('Y-m-d', strtotime('last day of' . $claimMonth));
        $paymentDate = date('Y-m-d', strtotime('last day of next month' . $claimMonth));
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $count = $query->first();

        $this->begin();
        //DB::unprepared('LOCK TABLES tClaim READ');

        if($count->count > 0){
            //既存データあり
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $upd->where('claimMonth', $strClaimMonth);
            $upd->update([
                'claimNo' => $this->getClaimNo(),
                'claimDate' => $claimDate,
                'paymentDate' => $paymentDate,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'updateDatetime' => $now,
            ]);

        }else{
            //既存データなし
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'claimMonth' => $strClaimMonth,
                'claimNo' => $this->getClaimNo(),
                'price' => $price,
                'claimDate' => $claimDate,
                'paymentDate' => $paymentDate,
                'claimStatus' => self::CLAIM_STATUS_DONE,
                'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                'createDatetime' => $now,
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
        $this->idUnitPrice = is_null($this->contractInfo['idUnitPrice']) ? 0 : $this->contractInfo['idUnitPrice'];

        // 検索単価
        $this->searchUnitPrice = is_null($this->contractInfo['searchUnitPrice']) ? 0 : $this->contractInfo['searchUnitPrice'];

        // 年間検索数
        $this->yearSearchCount = is_null($this->contractInfo['searchCount']) ? 0 : $this->contractInfo['searchCount'];

        // デポジット残高
        $this->deposit = is_null($this->contractInfo['deposit']) ? 0 : $this->contractInfo['deposit'];

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
            $this->trialSearchCount = $keywordHistoryModel->getSearchCount($data->companyId, $trialPlanId, null, $dateInfo['startTrial'], $dateInfo['endTrial']);
        }

        // 検索数取得
        $this->searchCount = 0;
        if ($trialPlanId != $this->contractInfo['contractPlanId']) {
            $this->searchCount = $keywordHistoryModel->getSearchCount($data->companyId, $this->contractInfo['contractPlanId'], null, $dateInfo['startUse'], $dateInfo['endUse']);
        }

        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
        switch ($this->contractInfo['contractTypeId']) {
            case self::TYPE_ALL_DEPOSIT:
                $ret = $this->calcAllDeposit($data, $dateInfo);
                break;

            case self::TYPE_ID_DEPOSIT:
                $ret = $this->calcIdDeposit($data, $dateInfo);
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
                    'totalPrice' => 0
                ];

        }

        return $ret;

    }

    /**
     * 全額デポジット計算
     *
     * @param $data
     * @param $dateInfo
     * @return array
     * @throws Exception
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcAllDeposit($data, $dateInfo): array
    {

        // トライル料金
        $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

        $idPrice = 0;
        $depositPrice = 0;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['startBeforeMonth']) {
            $idPrice = $this->idUnitPrice * $this->contractInfo['ids'] * 12;
            $depositPrice = $this->searchUnitPrice * $this->yearSearchCount;
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['startMonth']) {
            if ($this->getClaimStatus($data->companyId, $dateInfo['startDate']) === false) {
                $idPrice = $this->idUnitPrice * $this->contractInfo['ids'] * 12;
                $depositPrice = $this->searchUnitPrice * $this->yearSearchCount;
            }
        }

        // デポジット不足
        $payPerUse = $this->deposit - $this->searchUnitPrice * $this->searchCount;
        if ($payPerUse < 0) {
            $payPerUse = abs($payPerUse);
        }else{
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
                'amount' => 12,
                'unitPrice' => $this->idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => $this->searchUnitPrice,
                'unitPrice' => $this->yearSearchCount,
                'price' => $depositPrice,
            ],
            'payPerUse' =>[
                'amount' => ceil($payPerUse / $this->searchUnitPrice),
                'unitPrice' => $this->searchUnitPrice,
                'price' => $payPerUse,
            ],
            'totalPrice' => $totalPrice,
        ];

    }

    /**
     * ID台のみディポジット計算
     *
     * @param $data
     * @param $dateInfo
     * @return array
     * @throws Exception
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcIdDeposit($data, $dateInfo): array
    {
        // トライル料金
        $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

        $idPrice = 0;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['startBeforeMonth']) {
            $idPrice = $this->idUnitPrice * $this->contractInfo['ids'] * 12;
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['startMonth']) {
            if ($this->getClaimStatus($data->companyId, $dateInfo['startDate']) === false) {
                $idPrice = $this->idUnitPrice * $this->contractInfo['ids'] * 12;
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
                'amount' => 12,
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
        if($dateInfo['claimMonth'] >= $dateInfo['startMonth'] && $dateInfo['claimMonth'] <= $dateInfo['endMonth']){
            $idPrice = $this->idUnitPrice * $this->contractInfo['ids'];
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
                'amount' => 1,
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
        if (is_null($this->contractInfo['startTrial']) === false) {

            if ($dateInfo['prevMonthStartDate'] <= $this->contractInfo['startTrial'] && $this->contractInfo['startTrial'] < $dateInfo['startDate']) {
                // トライアル開始日は、請求前月スタートの場合は、請求月月初日とする
                $dateInfo['startTrial'] = $dateInfo['startDate'];

                // トライアル終了日は、請求前月スタートの場合は、利用開始の前日またはトライアル開始日の1か月後とする
                if (is_null($this->contractInfo['useStartDate'])) {
                    $dtUseStart = new Datetime($this->contractInfo['startTrial']);
                    $dtUseStart->modify('+1 month');
                } else {
                    $dtUseStart = new Datetime($this->contractInfo['useStartDate']);
                    $dtUseStart->modify('-1 day');
                }
                $dateInfo['endTrial'] = $dtUseStart->format('Y-m-d');

            }

            if ($dateInfo['startDate'] <= $this->contractInfo['startTrial'] && $this->contractInfo['startTrial'] < $dateInfo['endDate']) {
                // トライアル開始日は、請求月スタートの場合は、トライアル開始日とする
                $dateInfo['startTrial'] = $this->contractInfo['startTrial'];

                // トライアル終了日は、請求月スタートの場合は、請求月月末日または利用開始日前日とする
                $dateInfo['endTrial'] = $dateInfo['endDate'];
                if (is_null($this->contractInfo['useStartDate']) === false) {
                    if ($this->contractInfo['useStartDate'] < $dateInfo['endDate']) {
                        $dtUseStart = new Datetime($this->contractInfo['useStartDate']);
                        $dateInfo['endTrial'] = $dtUseStart->modify('-1 day')->format('Y-m-d');
                    }
                }
            }

        }

        // 利用日付
        $dateInfo['startUse'] = self::DATE_HIGH_VALUE;
        $dateInfo['endUse'] = $dateInfo['endDate'];
        $dateInfo['startBeforeMonth'] = substr(self::DATE_LOW_VALUE, 0, 7);
        $dateInfo['startMonth'] = substr(self::DATE_LOW_VALUE, 0, 7);
        $dateInfo['endMonth'] = substr($dateInfo['endDate'], 0, 7);
        if (is_null($this->contractInfo['useStartDate']) === false) {

            // 利用更新日が指定されている場合、利用更新日基準とする
            $workUseStartDate = $this->contractInfo['useStartDate'];
            if (is_null($this->contractInfo['useUpdateDate']) === false) {
                $workUseStartDate = $this->contractInfo['useUpdateDate'];
            }
            $dateInfo['startMonth'] = substr($workUseStartDate, 0, 7);
            $dateInfo['startBeforeMonth'] = (new Datetime($workUseStartDate))->modify('-1 month')->format('Y-m');

            // 請求月に利用開始になった場合
            if ($dateInfo['startDate'] <= $workUseStartDate && $workUseStartDate <= $dateInfo['endDate']) {
                $dateInfo['startUse'] = $workUseStartDate;
            } else {
                $dateInfo['startUse'] = $dateInfo['startDate'];
            }
        }


        return $dateInfo;
    }

    /**
     * 前月の請求有無
     *
     * @param $companyId
     * @param $date
     * @return bool $claim
     * @throws Exception
     */
    public function getClaimStatus($companyId, $date, $beforeMonth = true): bool
    {
        $date = new DateTime($date);
        if($beforeMonth === true){
            
            $prevDate = $date->modify("-1 month");
        }else{

            $prevDate = $date;
        }

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

        if(is_null($max)){
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
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $count = $query->first();

        $this->begin();

        if($count->count > 0){
            //既存データなし
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $upd->where('claimMonth', $strClaimMonth);
            $upd->update([
                'paymentDate' => $updateData[0]->paymentDate,
                'adjustNote' => $updateData[0]->adjustNote,
                'adjustPrice' => $updateData[0]->adjustPrice,
                'updateDatetime' => $now,
            ]);

        }else{
            //既存データあり
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'claimMonth' => $strClaimMonth,
                'price' => $updateData[0]->price,
                'claimStatus' => self::CLAIM_STATUS_UNDONE,
                'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                'paymentDate' => $updateData[0]->paymentDate,
                'adjustNote' => $updateData[0]->adjustNote,
                'adjustPrice' => $updateData[0]->adjustPrice,
                'createDatetime' => $now,
                'updateDatetime' => $now,
            ]);
        }

        //WEBプラン
        if(is_null($updateData[0]->webPlanDeposit) === false){
            //契約データあり
            $updContract = DB::table('tContractPlan');
            $updContract->where('companyId', $updateData[0]->webPlanCompanyId);
            $updContract->where('contractPlanId', $updateData[0]->webPlanPlanId);
            $updContract->update([
                'deposit' => $updateData[0]->webPlanDeposit,
                'updateDatetime' => $now,
            ]);
        }

        //APIプラン
        if(is_null($updateData[0]->apiPlanDeposit) === false){
            //契約データあり
            $updContract = DB::table('tContractPlan');
            $updContract->where('companyId', $updateData[0]->apiPlanCompanyId);
            $updContract->where('contractPlanId', $updateData[0]->apiPlanPlanId);
            $updContract->update([
                'deposit' => $updateData[0]->apiPlanDeposit,
                'updateDatetime' => $now,
            ]);
        }

        $this->commit();
    }

}