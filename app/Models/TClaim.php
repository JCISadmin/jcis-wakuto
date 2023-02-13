<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Datetime;

/**
 * 請求マスタ
 */
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

    /** 料金情報 @var array|null */
    protected ?array $chargeInfo;

    /** 契約情報 @var string|null */
    protected ?string $contractTypeId;

    /** ID数量 @var integer */
    private int $ids;

    /** ID単価 @var integer */
    private int $idUnitPrice;

    /** 検索情報 @var array|null */
    private ?array $searchInfo;

    /** 課金検索情報 @var array|null */
    private ?array $chargeSearchInfo;

    /** 年間検索数 @var integer */
    private int $yearSearchCount;

    /** 年間検索数適用単価 @var integer */
    private int $yearSearchUnitPrice;

    /** デポジット残高 @var integer */
    private int $deposit;

    /** トライアル検索単価 @var integer  */
    private int $trialUnitPrice;

    /** トライアル検索数 @var integer  */
    private int $trialSearchCount;

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
        $mContractTypeModel = new MContractType();
        $acurisClaimModel = new AcurisClaim();

        $strClaimMonth = str_replace('-', '', $claimMonth);

        $month = new DateTime($claimMonth);
        $startMonth = $month->format('Y-m-01');
        $endMonth = $month->format('Y-m-t');

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


        $claim = DB::table('tClaim');
        $claim->select(
            'tClaim.*',
        );
        $claim->where('claimMonth', $strClaimMonth);

        $user = DB::table('mUserCompany');
        $user->select(
            'mUserCompany.companyId',
            'mUserCompany.name as mUserName',
            'mUserCompany.kana as mUserKana',
            'mUserCompany.postCode as mUserPostCode',
            'mUserCompany.address as mUserAddress',
            'mUserCompany.tel as mUserTel',
            'mUserCompany.staffName',
            'mUserCompany.staffDepartmentJob',
            'mUserCompany.staffTel',
            'mUserCompany.staffMail',
            'mUserCompany.claimName as mUserClaimName',
            'mUserCompany.claimDepartmentJob as mUserClaimDepartmentJob',
            'mUserCompany.claimTel as mUserClaimTel',
            'mUserCompany.claimMailTo as mUserClaimMailTo',
            'mUserCompany.claimMailCc as mUserClaimMailCc',
            'mUserCompany.claimMailBcc as mUserClaimMailBcc',
            'mUserCompany.deliveryDate',
            'mUserCompany.paymentTerm',
            'mUserCompany.chargeName as mUserChargeName',
            'mUserCompany.chargeMail as mUserChargeMail',
            'mUserCompany.contractStatus',
            'mUserCompany.delFlg',
            'mUserCompany.createDatetime',
            'mUserCompany.updateDatetime',
            'webPlan.companyId as webCompanyId',
            'webPlan.contractPlanId as webContractPlanId',
            'webPlan.contractPlanName as webContractPlanName',
            'webPlan.planType as webPlanType',
            'webPlan.trialSearchUnitPrice as webTrialSearchUnitPrice',
            'webPlan.deposit as webDeposit',
            'apiPlan.companyId as apiCompanyId',
            'apiPlan.contractPlanId as apiContractPlanId',
            'apiPlan.contractPlanName as apiContractPlanName',
            'apiPlan.planType as apiPlanType',
            'apiPlan.trialSearchUnitPrice as apiTrialSearchUnitPrice',
            'apiPlan.deposit as apiDeposit',
            'mContractStatus.name as statusName',
            'claim.claimNo',
            'claim.price',
            'claim.claimNote',
            'claim.claimStatus',
            'claim.paymentStatus',
            'claim.claimDate',
            'claim.deliveryDate as claimDeliveryDate',
            'claim.paymentDate',
            'claim.memo as claimMemo',
            'claim.name',
            'claim.postCode',
            'claim.address',
            'claim.tel',
            'claim.chargeName',
            'claim.chargeMail',
            'claim.claimName',
            'claim.claimDepartmentJob',
            'claim.claimTel',
            'claim.claimMailTo',
            'claim.claimMailCc',
            'claim.claimMailBcc',
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
            $query->where('mUserName', 'like', '%' . $companyName . '%');
        }

        if(is_null($companyIds) === false){
            $idAry = [];
            foreach($companyIds as $id){
                $idAry[] = $id;
            }
            $query->whereIn('companyId', $idAry);
        }

        //50音順
        $query->orderByRaw('mUserKana IS NULL ASC');
        $query->orderBy('mUserKana','ASC');

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
            //ID数を取得
            $webContractPlan = $contractPlanModel->getPlan($items->webCompanyId, self::PLAN_TYPE_WEB, '', $claimMonth);
            if(is_null($webContractPlan)){
                $list[$key]->webIds = 0;
            }else{
                $list[$key]->webIds = $webContractPlan['ids'];
            }

            $apiContractPlan = $contractPlanModel->getPlan($items->apiCompanyId, self::PLAN_TYPE_API, '', $claimMonth);
            if(is_null($apiContractPlan)){
                $list[$key]->apiIds = 0;
            }else{
                $list[$key]->apiIds = $apiContractPlan['ids'];
            }

            //月間検索数を取得
            $list[$key]->webMonthSearchCount = $keywordHistoryModel->getSearchCount($items->webCompanyId, $items->webPlanType, null, $startMonth, $endMonth);
            $list[$key]->apiMonthSearchCount = $keywordHistoryModel->getSearchCount($items->apiCompanyId, $items->apiPlanType, null, $startMonth, $endMonth);
            
            //契約情報一覧を取得
            $tContractDetailPlanModel = new TContractPlanDetail();
            $list[$key]->webContractInfo = $tContractDetailPlanModel->getContractInfo($items->webCompanyId, $claimMonth, self::PLAN_TYPE_WEB);
            $list[$key]->apiContractInfo = $tContractDetailPlanModel->getContractInfo($items->apiCompanyId, $claimMonth, self::PLAN_TYPE_API);

            //WEB検索契約の請求額を取得
            $webDeposit = is_null($items->webDeposit) ? 0 : $items->webDeposit;
            $this->deposit = $webDeposit;
            $webPrice = $this->getPrice($claimMonth, $items, $items->webPlanType);

            //API検索契約の請求額を取得
            $apiDeposit = is_null($items->apiDeposit) ? 0 : $items->apiDeposit;
            $this->deposit = $apiDeposit;
            $apiPrice = $this->getPrice($claimMonth, $items, $items->apiPlanType);

            //最新の契約形態を取得
            $list[$key]->webContractTypeId = $webPrice['contractType'];
            $list[$key]->webContractTypeName = $mContractTypeModel->getTypeNameByTypeId($webPrice['contractType']);
            $list[$key]->apiContractTypeId = $apiPrice['contractType'];
            $list[$key]->apiContractTypeName = $mContractTypeModel->getTypeNameByTypeId($apiPrice['contractType']);
            
            $list[$key]->items = [
                'web' => $webPrice,
                'api' => $apiPrice,
            ];

            // 海外検索契約(Acuris)の請求額を取得
            $list[$key]->acurisItems = $acurisClaimModel->getPrice($claimMonth, $items);

            //請求額(補正額抜き・税抜き)
            if(is_null($list[$key]->claimNo)){
                //請求データ無(または一時保存データの場合)
                $price = $webPrice['totalPrice'] + $apiPrice['totalPrice'] + $list[$key]->acurisItems['totalPrice'];
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

            //未作成未請求データ または 作成済み未請求データ の場合 、ユーザーの入力値を使用
            if(is_null($items->claimNo) || $items->claimStatus === 0){
                //ユーザー情報
                $items->name = $items->mUserName;
                $items->postCode = $items->mUserPostCode;
                $items->address = $items->mUserAddress;
                $items->tel = $items->mUserTel;
                $items->chargeName = $items->mUserChargeName;
                $items->chargeMail = $items->mUserChargeMail;
                $items->claimName = $items->mUserClaimName;
                $items->claimDepartmentJob = $items->mUserClaimDepartmentJob;
                $items->claimTel = $items->mUserClaimTel;
                $items->claimMailTo = $items->mUserClaimMailTo;
                $items->claimMailCc = $items->mUserClaimMailCc;
                $items->claimMailBcc = $items->mUserClaimMailBcc;
            }

            //未作成未請求データ の場合
            if(is_null($items->claimNo)){
                //支払期限: 請求月から算出
                if(is_null($items->paymentTerm)){
                    //支払期限（請求翌月末）をセット
                    $items->paymentDate = date('Y-m-d', strtotime('last day of next month' . $claimMonth));
                }else{
                    //支払期限（ユーザー詳細 設定値）をセット
                    $items->paymentDate = new DateTime($claimMonth);
                    $items->paymentDate->modify(config('hds.user.paymentTerm.'.$items->paymentTerm.'.modify'));
                    $items->paymentDate = $items->paymentDate->format('Y-m-d');
                }

                //送付期限: mUserCompanyの値を使用
                $items->claimDeliveryDate = $items->deliveryDate;

                // 備考欄: configの値を使用
                $items->claimNote = config('note.claim.claimNote');
            }

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
        $companyIds[] = $companyId;
        $claimData = $this->getList($claimMonth, null, $companyIds, null, false, false);
        $calcPrice = $claimData[0]->priceWithoutTax;

        //前払いステータス
        $webPrepaidStatus = $this->getPrepaidStatusValue($claimData, self::PLAN_TYPE_WEB);
        $apiPrepaidStatus = $this->getPrepaidStatusValue($claimData, self::PLAN_TYPE_API);

        $dt = new Datetime();
        $now = $dt->format('Ymd');
        //発行日（請求月末）
        $claimDate = date('Y-m-d');
        //請求月（YYYYMM）
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $count = $query->first();

        $lockName = 'claimLock';
        $timeOut = 300;
        $claimModel = new Claim();
        $tClaimDetailModel = new TClaimDetail();

        $this->begin();

        try{
            $lock = DB::select('select get_lock(?, ?) as result', [$lockName, $timeOut]);
            if ($lock[0]->result === 1) {
                //ロック取得成功

                if ($count->count > 0) {
                    //既存データあり
                    $updateColumn = [
                        'claimStatus' => self::CLAIM_STATUS_DONE,
                        'name' => $claimData[0]->name,
                        'postCode' => $claimData[0]->postCode,
                        'address' => $claimData[0]->address,
                        'tel' => $claimData[0]->tel,
                        'chargeName' => $claimData[0]->chargeName,
                        'chargeMail' => $claimData[0]->chargeMail,
                        'claimName' => $claimData[0]->claimName,
                        'claimDepartmentJob' => $claimData[0]->claimDepartmentJob,
                        'claimTel' => $claimData[0]->claimTel,
                        'claimMailTo' => $claimData[0]->claimMailTo,
                        'claimMailCc' => $claimData[0]->claimMailCc,
                        'claimMailBcc' => $claimData[0]->claimMailBcc,
                        'updateDatetime' => $now,
                    ];

                    $isClaimNo = $this->isClaimNo($companyId, $strClaimMonth);

                    // 更新対象に請求番号が存在しない場合(一時保存データの場合)
                    if (!$isClaimNo) {
                        // TClaim更新対象を追加
                        $addUpdateColumn = [
                            'claimNo' => $this->getClaimNo(),
                            'price' => $calcPrice,
                            'claimDate' => $claimDate,
                            'paymentDate' => $claimData[0]->paymentDate,
                            'deliveryDate' => $claimData[0]->deliveryDate,
                            'webPrepaidStatus' => $webPrepaidStatus,
                            'apiPrepaidStatus' => $apiPrepaidStatus,
                            'claimNote' => $claimData[0]->claimNote,
                        ];
                        $updateColumn = array_merge($updateColumn, $addUpdateColumn);

                        // 請求費目更新
                        $expenseList = $claimModel->getExpenseList($companyIds, $claimMonth);
                        $tClaimDetailModel->claimUpdate($companyId, $claimMonth, $expenseList);
                    }

                    $upd = DB::table($this->table);
                    $upd->where('companyId', $companyId);
                    $upd->where('claimMonth', $strClaimMonth);
                    $upd->update($updateColumn);

                } else {
                    // 請求費目更新
                    $expenseList = $claimModel->calcExpenseList($companyIds, $claimMonth);
                    $tClaimDetailModel->claimUpdate($companyId, $claimMonth, $expenseList);

                    //既存データなし
                    $ins = DB::table($this->table);
                    $ins->insert([
                        'companyId' => $companyId,
                        'claimMonth' => $strClaimMonth,
                        'claimNo' => $this->getClaimNo(),
                        'price' => $calcPrice,
                        'claimDate' => $claimDate,
                        'paymentDate' => $claimData[0]->paymentDate,
                        'deliveryDate' => $claimData[0]->deliveryDate,
                        'claimStatus' => self::CLAIM_STATUS_DONE,
                        'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,
                        'claimNote' => $claimData[0]->claimNote,
                        'name' => $claimData[0]->name,
                        'postCode' => $claimData[0]->postCode,
                        'address' => $claimData[0]->address,
                        'tel' => $claimData[0]->tel,
                        'chargeName' => $claimData[0]->chargeName,
                        'chargeMail' => $claimData[0]->chargeMail,
                        'claimName' => $claimData[0]->claimName,
                        'claimDepartmentJob' => $claimData[0]->claimDepartmentJob,
                        'claimTel' => $claimData[0]->claimTel,
                        'claimMailTo' => $claimData[0]->claimMailTo,
                        'claimMailCc' => $claimData[0]->claimMailCc,
                        'claimMailBcc' => $claimData[0]->claimMailBcc,
                        'createDatetime' => $now,
                        'updateDatetime' => $now,
                    ]);


                }
            }
        } finally {
            DB::select('select release_lock(?)', [$lockName]);

        }

        $this->commit();
    }

    /**
     * 請求ステータスを未請求に変更
     *
     * @param $companyId
     * @param $claimMonth
     * @throws Exception
     */
    public function changeNotClaimStatus($companyId, $claimMonth)
    {
        $dt = new Datetime();
        $now = $dt->format('Ymd');
        //請求月（YYYYMM）
        $strClaimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);

        $lockName = 'claimLock';
        $timeOut = 300;

        $this->begin();

        try{
            $lock = DB::select('select get_lock(?, ?) as result', [$lockName, $timeOut]);
            if ($lock[0]->result === 1) {
                //ロック取得成功

                    //既存データあり
                    $upd = DB::table($this->table);
                    $upd->where('companyId', $companyId);
                    $upd->where('claimMonth', $strClaimMonth);
                    $upd->update([
                        'claimStatus' => self::CLAIM_STATUS_UNDONE,
                        'updateDatetime' => $now,
                    ]);
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
     * 入金ステータスを未入金に変更
     *
     * @param $companyId
     * @param $claimMonth
     * @throws Exception
     */
    public function changeNotPaymentStatus($companyId, $claimMonth)
    {
        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $claimMonth = str_replace('-', '', $claimMonth);

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $claimMonth);
        $query->update([
            'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
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
     * @return array
     * @throws Exception
     */
    public function getPrice($claimMonth, $data, $planType): array
    {
        // モデルインスタンスの取得
        $mContractPlanModel = new MContractPlan();
        $keywordHistoryModel = new TKeywordHistory();
        $tContractPlanModel = new TContractPlan();
        $tContractDetailPlanModel = new TContractPlanDetail();
        $startDate = date('Y-m-d', strtotime('first day of this month' . $claimMonth));
        $endDate = date('Y-m-d', strtotime('last day of this month' . $claimMonth));
        
        // 契約情報
        $this->contractInfo = $tContractPlanModel->getPlan($data->companyId, $planType, '', $claimMonth);
        // 契約プランがない場合 空データを生成
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
                    [
                        'amount' => 0,
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
                    'total' => 0,
                ],
                'totalPrice' => 0,
                'contractType' => NULL,
            ];
        }

        // 請求日付情報取得
        $dateInfo = $this->getClaimDateInfo($claimMonth);

        //契約履歴情報を取得
        $detailList = $tContractDetailPlanModel->getDetailByMonth($data->companyId, $startDate, $endDate, $planType);

        $this->chargeInfo['searchInfo'] = [];
        $this->chargeInfo['chargeSearchInfo'] = [];
        $this->chargeInfo['idUnitPrice'] = 0;
        $this->chargeInfo['searchCount'] = 0;
        $this->chargeInfo['yearSearchUnitPrice'] = 0;
        $this->chargeInfo['contractTypeId'] = NULL;

        foreach($detailList as $detail){
            // 契約適用開始日/終了日が月初/月末を超過する場合 月初/月末に調整
            if($dateInfo['startDate'] > $detail->contractStartDate){
                $contractStartDate = $dateInfo['startDate'];
            }else{
                $contractStartDate = $detail->contractStartDate;
            }
            if($dateInfo['endDate'] < $detail->contractEndDate){
                $contractEndDate = $dateInfo['endDate'];
            }else{
                $contractEndDate = $detail->contractEndDate;
            }
            
            //検索単価(履歴別)と紐づく検索数を取得
            $this->chargeInfo['searchInfo'][] = [
                'searchUnitPrice' => $detail->searchUnitPrice,
                'searchCount' => $keywordHistoryModel->getSearchCount($data->companyId, $this->contractInfo['contractDetail']['planType'], null, date_format(new DateTime($contractStartDate), 'Y-m-d 0:00:00'), date_format(new DateTime($contractEndDate), 'Y-m-d 23:59:59')),
                'contractTypeId' => $detail->contractTypeId,
            ];

            //検索単価(履歴別)と紐づく課金対象の検索数を取得
            $this->chargeInfo['chargeSearchInfo'][] = [
                'searchUnitPrice' => $detail->searchUnitPrice,
                'searchCount' => $keywordHistoryModel->getChargeSearchCount($data->companyId, $this->contractInfo['contractDetail']['planType'], null, date_format(new DateTime($contractStartDate), 'Y-m-d 0:00:00'), date_format(new DateTime($contractEndDate), 'Y-m-d 23:59:59')),
                'contractTypeId' => $detail->contractTypeId,
            ];

        }

        // ID代/年検索数/年検索数適用単価/契約形態 は指定期間内で最大seqNoのレコードから使用 
        if($detailList !== []){
            $lastDetail = end($detailList);
            $this->chargeInfo['idUnitPrice'] = $lastDetail->idUnitPrice;
            $this->chargeInfo['searchCount'] = $lastDetail->searchCount;
            $this->chargeInfo['yearSearchUnitPrice'] = $lastDetail->searchUnitPrice;
            $this->chargeInfo['contractTypeId'] = $lastDetail->contractTypeId;
        }

        // 契約情報の補正
        // ID単価
        $this->ids = $this->contractInfo['ids'];
        $this->idUnitPrice = is_null($this->chargeInfo['idUnitPrice']) ? 0 : $this->chargeInfo['idUnitPrice'];

        // 検索情報
        $this->searchInfo = [];
        foreach($this->chargeInfo['searchInfo'] as $searchItem){

            $this->searchInfo[] = is_null($searchItem) ? [] : $searchItem;
        }

        // 課金対象の検索情報
        $this->chargeSearchInfo = [];
        foreach($this->chargeInfo['chargeSearchInfo'] as $chargeSearchItem){

            $this->chargeSearchInfo[] = is_null($chargeSearchItem) ? [] : $chargeSearchItem;
        }

        // 年間検索数
        $this->yearSearchCount = is_null($this->chargeInfo['searchCount']) ? 0 : $this->chargeInfo['searchCount'];
        // 年間検索数適用単価
        $this->yearSearchUnitPrice = is_null($this->chargeInfo['yearSearchUnitPrice']) ? 0 : $this->chargeInfo['yearSearchUnitPrice'];

        // トライアル関連
        $this->trialUnitPrice = 0;
        $this->trialSearchCount = 0;
        $trialPlanId = config('hds.contract.trialPlan.'.$planType);
        if($trialPlanId !== '') {
            $planInfo = $mContractPlanModel->get($trialPlanId);
            $this->trialUnitPrice = $planInfo->unitPrice;
            
            //ユーザー詳細のトライアル検索単価がある場合
            if(!is_null($this->contractInfo['trialSearchUnitPrice'])){
                $this->trialUnitPrice = $this->contractInfo['trialSearchUnitPrice'];
            }

            // トライアル検索数取得
            $this->trialSearchCount = $keywordHistoryModel->getSearchCount($data->companyId, $this->contractInfo['contractDetail']['planType'], null, date_format(new DateTime($dateInfo['startTrial']), 'Y-m-d 0:00:00'), date_format(new DateTime($dateInfo['endTrial']), 'Y-m-d 23:59:59'));
        }

        //適用契約形態
        $this->contractTypeId = $this->chargeInfo['contractTypeId'];

        /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
        switch ($this->chargeInfo['contractTypeId']) {
            case self::TYPE_ALL_DEPOSIT:
                $ret = $this->calcAllDeposit($data, $dateInfo, $planType);
                break;

            case self::TYPE_ID_DEPOSIT:
                $ret = $this->calcIdDeposit($data, $dateInfo, $planType);
                break;

            case self::TYPE_MONTHLY:
                $ret = $this->calcMonthly($data, $dateInfo, $planType);
                break;

            default:
                //請求月に本契約が含まれない場合

                //トライアルのみ計算
                $trialSearchCount = $this->trialSearchCount;
                $trialUnitPrice = $this->trialUnitPrice;
                $trialPrice = $this->trialSearchCount * $this->trialUnitPrice;

                $ids = 0;
                $idUnitPrice = 0;
                $idPrice = 0;

                $yearSearchCount = 0;
                $yearSearchUnitPrice = 0;

                $depositPrice = 0;

                $contractTypeId = NULL;

                // 前払い ID/デポジットを来月請求から取得
                if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
                    // 請求月翌月が契約更新月の時
                    $nextMonthPrice = $this->getPrice($dateInfo['updateMonth'], $data, $planType);
                    //デポジットのみ、前払い適用外
                    if($nextMonthPrice['contractType'] == self::TYPE_ALL_DEPOSIT || $nextMonthPrice['contractType'] == self::TYPE_ID_DEPOSIT){

                        // ID代
                        $ids = $nextMonthPrice['id']['amount'];
                        $idUnitPrice = $nextMonthPrice['id']['unitPrice'];
                        $idPrice = $nextMonthPrice['id']['price'];

                        // 年間検索数
                        $yearSearchCount = $nextMonthPrice['deposit']['amount'];
                        $yearSearchUnitPrice = $nextMonthPrice['deposit']['unitPrice'];

                        // デポジット代(全額デポのみ)
                        if($nextMonthPrice['contractType'] === self::TYPE_ALL_DEPOSIT){
                            $depositPrice = $nextMonthPrice['deposit']['price'];
                        }

                        // 契約形態
                        $contractTypeId = $nextMonthPrice['contractType'];

                    }
                }

                $totalPrice = $trialPrice + $idPrice + $depositPrice;

                $ret = [
                    'trial' => [
                        'amount' => $trialSearchCount,
                        'unitPrice' => $trialUnitPrice,
                        'price' => $trialPrice,
                    ],
                    'id' => [
                        'amount' => $ids,
                        'unitPrice' => $idUnitPrice,
                        'price' => $idPrice,
                    ],
                    'deposit' =>[
                        'amount' => $yearSearchCount,
                        'unitPrice' => $yearSearchUnitPrice,
                        'price' => $depositPrice,
                    ],
                    //契約変更数分表示
                    'payPerUse' =>[
                        [
                            'amount' => 0,
                            'unitPrice' => 0,
                            'price' => 0,
                        ],
                        'total' => 0,
                    ],
                    'overageCharges' => 0,
                    'totalPrice' => $totalPrice,
                    'contractType' => $contractTypeId,
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
        // トライアル料金
        $trialSearchCount = $this->trialSearchCount;
        $trialUnitPrice = $this->trialUnitPrice;
        $trialPrice = $trialSearchCount * $trialUnitPrice;

        //課金額
        $overageCharges = 0;
        $chargeSearchCount = 0;
        foreach($this->chargeSearchInfo as $chargeSearchItem){

            $overageCharges += $chargeSearchItem['searchUnitPrice'] * $chargeSearchItem['searchCount'];
            $chargeSearchCount += $chargeSearchItem['searchCount'];

            $payPerUseAry[] = [
                'amount' => $chargeSearchItem['searchCount'],
                'unitPrice' => $chargeSearchItem['searchUnitPrice'],
                'price' => $chargeSearchItem['searchUnitPrice'] * $chargeSearchItem['searchCount'],
            ];
        }

        //ID代
        $idPrice = 0;
        //全額デポジットの場合、ID代単価を1年分とする
        $this->idUnitPrice *= 12;
        $ids = $this->ids;
        $idUnitPrice = $this->idUnitPrice;

        // 年間検索数
        $yearSearchCount = $this->yearSearchCount;
        $yearSearchUnitPrice = $this->yearSearchUnitPrice;

        // デポジット不足
        $depositPrice = 0;
        $payPerUse = $overageCharges;

        // 契約形態
        $contractTypeId = $this->contractTypeId;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
            //来月の契約情報を参照
            $nextMonthPrice = $this->getPrice($dateInfo['updateMonth'], $data, $planType);
            //デポジットのみ、前払い適用外
            if($nextMonthPrice['contractType'] == self::TYPE_ALL_DEPOSIT || $nextMonthPrice['contractType'] == self::TYPE_ID_DEPOSIT){

                // ID代
                $ids = $nextMonthPrice['id']['amount'];
                $idUnitPrice = $nextMonthPrice['id']['unitPrice'];
                $idPrice = $nextMonthPrice['id']['price'];

                // 年間検索数
                $yearSearchCount = $nextMonthPrice['deposit']['amount'];
                $yearSearchUnitPrice = $nextMonthPrice['deposit']['unitPrice'];

                // デポジット代(全額デポのみ)
                if($nextMonthPrice['contractType'] === self::TYPE_ALL_DEPOSIT){
                    $depositPrice = $nextMonthPrice['deposit']['price'];
                }

                // 契約形態
                $contractTypeId = $nextMonthPrice['contractType'];
            }
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateMonth']) {
            if ($this->getPrepaidStatus($data->companyId, $planType, $dateInfo['updateBeforeMonth']) === false) {
                $idPrice = $this->idUnitPrice * $this->ids;
                $depositPrice = $this->yearSearchUnitPrice * $this->yearSearchCount;
            }
        }

        $payPerUseAry['total'] = $payPerUse;

        $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;

        return  [
            'trial' => [
                'amount' => $trialSearchCount,
                'unitPrice' => $trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $ids,
                'unitPrice' => $idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => $yearSearchCount,
                'unitPrice' => $yearSearchUnitPrice,
                'price' => $depositPrice,
            ],
            'payPerUse' => $payPerUseAry,
            'overageCharges' => $overageCharges,
            'totalPrice' => $totalPrice,
            'contractType' => $contractTypeId,
        ];

    }

    /**
     * ID代のみディポジット計算
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
        // トライアル料金
        $trialSearchCount = $this->trialSearchCount;
        $trialUnitPrice = $this->trialUnitPrice;
        $trialPrice = $trialSearchCount * $trialUnitPrice;

        //検索料金
        $payPerUse = 0;
        foreach($this->searchInfo as $searchItem){

            $payPerUseAry[] = [
                'amount' => $searchItem['searchCount'],
                'unitPrice' => $searchItem['searchUnitPrice'],
                'price' => $searchItem['searchUnitPrice'] * $searchItem['searchCount'],
            ];

            $payPerUse += $searchItem['searchUnitPrice'] * $searchItem['searchCount'];
        }

        // ID代
        $idPrice = 0;
        // ID代のみデポジットの場合、ID代単価を1年分とする
        $this->idUnitPrice *= 12;
        $ids = $this->ids;
        $idUnitPrice = $this->idUnitPrice;

        // 年間検索数
        $yearSearchCount = $this->yearSearchCount;
        $yearSearchUnitPrice = $this->yearSearchUnitPrice;

        // デポジット代
        $depositPrice = 0;

        // 契約形態
        $contractTypeId = $this->contractTypeId;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
            //来月の契約情報を参照
            $nextMonthPrice = $this->getPrice($dateInfo['updateMonth'], $data, $planType);
            //デポジットのみ、前払い適用外
            if($nextMonthPrice['contractType'] == self::TYPE_ALL_DEPOSIT || $nextMonthPrice['contractType'] == self::TYPE_ID_DEPOSIT){

                // ID代
                $ids = $nextMonthPrice['id']['amount'];
                $idUnitPrice = $nextMonthPrice['id']['unitPrice'];
                $idPrice = $nextMonthPrice['id']['price'];

                // 年間検索数
                $yearSearchCount = $nextMonthPrice['deposit']['amount'];
                $yearSearchUnitPrice = $nextMonthPrice['deposit']['unitPrice'];

                // デポジット代(全額デポのみ)
                if($nextMonthPrice['contractType'] === self::TYPE_ALL_DEPOSIT){
                    $depositPrice = $nextMonthPrice['deposit']['price'];
                }

                // 契約形態
                $contractTypeId = $nextMonthPrice['contractType'];
            }
        }

        // 前月未払い前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateMonth']) {
            if ($this->getPrepaidStatus($data->companyId, $planType, $dateInfo['updateBeforeMonth']) === false) {
                $idPrice = $this->idUnitPrice * $this->ids;
            }
        }

        $payPerUseAry['total'] = $payPerUse;

        $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;

        return [
            'trial' => [
                'amount' => $trialSearchCount,
                'unitPrice' => $trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $ids,
                'unitPrice' => $idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => $yearSearchCount,
                'unitPrice' => $yearSearchUnitPrice,
                'price' => $depositPrice,
            ],
            'payPerUse' => $payPerUseAry,
            'overageCharges' => 0,
            'totalPrice' => $totalPrice,
            'contractType' => $contractTypeId,
        ];

    }

    /**
     * 毎月請求の計算
     *
     * @param $dateInfo
     * @return array
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    private function calcMonthly($data, $dateInfo, $planType): array
    {
        // トライアル料金
        $trialSearchCount = $this->trialSearchCount;
        $trialUnitPrice = $this->trialUnitPrice;
        $trialPrice = $trialSearchCount * $trialUnitPrice;

        //検索料金
        $payPerUse = 0;
        foreach($this->searchInfo as $searchItem){
    
            $payPerUseAry[] = [
                'amount' => $searchItem['searchCount'],
                'unitPrice' => $searchItem['searchUnitPrice'],
                'price' => $searchItem['searchUnitPrice'] * $searchItem['searchCount'],
            ];
    
            $payPerUse += $searchItem['searchUnitPrice'] * $searchItem['searchCount'];
        }

        // ID代
        $idPrice = 0;
        $ids = $this->ids;
        $idUnitPrice = $this->idUnitPrice;
        if(is_null($dateInfo['startMonth']) === false){
            if($dateInfo['claimMonth'] >= $dateInfo['startMonth'] && $dateInfo['claimMonth'] <= $dateInfo['endMonth']){
                $idPrice = $this->idUnitPrice * $this->ids;
            }
        }

        // 年間検索数
        $yearSearchCount = $this->yearSearchCount;
        $yearSearchUnitPrice = $this->yearSearchUnitPrice;

        // デポジット代
        $depositPrice = 0;

        // 契約形態
        $contractTypeId = $this->contractTypeId;

        // 前払い
        if ($dateInfo['claimMonth'] === $dateInfo['updateBeforeMonth']) {
            //来月の契約情報を参照
            $nextMonthPrice = $this->getPrice($dateInfo['updateMonth'], $data, $planType);
            //デポジットのみ、前払い適用外
            if($nextMonthPrice['contractType'] == self::TYPE_ALL_DEPOSIT || $nextMonthPrice['contractType'] == self::TYPE_ID_DEPOSIT){

                // ID代
                $ids = $nextMonthPrice['id']['amount'];
                $idUnitPrice = $nextMonthPrice['id']['unitPrice'];
                $idPrice = $nextMonthPrice['id']['price'];

                // 年間検索数
                $yearSearchCount = $nextMonthPrice['deposit']['amount'];
                $yearSearchUnitPrice = $nextMonthPrice['deposit']['unitPrice'];

                // デポジット代(全額デポのみ)
                if($nextMonthPrice['contractType'] === self::TYPE_ALL_DEPOSIT){
                    $depositPrice = $nextMonthPrice['deposit']['price'];
                }

                // 契約形態
                $contractTypeId = $nextMonthPrice['contractType'];
            }
        }

        $payPerUseAry['total'] = $payPerUse;

        $totalPrice = $trialPrice + $payPerUse + $idPrice + $depositPrice;

        return [
            'trial' => [
                'amount' => $trialSearchCount,
                'unitPrice' => $trialUnitPrice,
                'price' => $trialPrice,
            ],
            'id' => [
                //amountを期間(〇カ月)→ID数量に変更(2022/6/28)
                'amount' => $ids,
                'unitPrice' => $idUnitPrice,
                'price' => $idPrice,
            ],
            'deposit' =>[
                'amount' => $yearSearchCount,
                'unitPrice' => $yearSearchUnitPrice,
                'price' => $depositPrice,
            ],
            'payPerUse' => $payPerUseAry,
            'overageCharges' => 0,
            'totalPrice' => $totalPrice,
            'contractType' => $contractTypeId,
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

                // 請求費目更新
                $expenseList = array_merge($updateData['detail']['expense'], $updateData['detail']['adjust']);
                $tClaimDetailModel->claimUpdate($companyId, $claimMonth, $expenseList);

                // 請求費目から合計金額を算出
                $calcPrice = $tClaimDetailModel->getCalcPrice($companyId, $claimMonth);

                if($count->count > 0){
                    //既存データあり
                    $updateColumn = [
                        'price' => $calcPrice,
                        'claimDate' => $updateData['claimDate'],
                        'deliveryDate' => $updateData['deliveryDate'],
                        'paymentDate' => $updateData['paymentDate'],
                        'claimNote' => $updateData['claimNote'],
                        'memo' => $updateData['memo'],
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,
                        'name' => $claimList[0]->name,
                        'postCode' => $claimList[0]->postCode,
                        'address' => $claimList[0]->address,
                        'tel' => $claimList[0]->tel,
                        'chargeName' => $claimList[0]->chargeName,
                        'chargeMail' => $claimList[0]->chargeMail,
                        'claimName' => $claimList[0]->claimName,
                        'claimDepartmentJob' => $claimList[0]->claimDepartmentJob,
                        'claimTel' => $claimList[0]->claimTel,
                        'claimMailTo' => $claimList[0]->claimMailTo,
                        'claimMailCc' => $claimList[0]->claimMailCc,
                        'claimMailBcc' => $claimList[0]->claimMailBcc,
                        'updateDatetime' => $now,    
                    ];

                    $isClaimNo = $this->isClaimNo($companyId, $strClaimMonth);

                    // 更新対象に請求番号が存在しない場合(一時保存データの場合)
                    if (!$isClaimNo) {
                        // 更新対象に請求番号を追加
                        $updateColumn = array_merge($updateColumn, ['claimNo' => $this->getClaimNo()]);
                    }

                    $upd = DB::table($this->table);
                    $upd->where('companyId', $companyId);
                    $upd->where('claimMonth', $strClaimMonth);
                    $upd->update($updateColumn);

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
                        'deliveryDate' => $updateData['deliveryDate'],
                        'paymentDate' => $updateData['paymentDate'],
                        'claimNote' => $updateData['claimNote'],
                        'memo' => $updateData['memo'],
                        'webPrepaidStatus' => $webPrepaidStatus,
                        'apiPrepaidStatus' => $apiPrepaidStatus,
                        'name' => $claimList[0]->name,
                        'postCode' => $claimList[0]->postCode,
                        'address' => $claimList[0]->address,
                        'tel' => $claimList[0]->tel,
                        'chargeName' => $claimList[0]->chargeName,
                        'chargeMail' => $claimList[0]->chargeMail,
                        'claimName' => $claimList[0]->claimName,
                        'claimDepartmentJob' => $claimList[0]->claimDepartmentJob,
                        'claimTel' => $claimList[0]->claimTel,
                        'claimMailTo' => $claimList[0]->claimMailTo,
                        'claimMailCc' => $claimList[0]->claimMailCc,
                        'claimMailBcc' => $claimList[0]->claimMailBcc,
                        'createDatetime' => $now,
                        'updateDatetime' => $now,
                    ]);
                }

            }
        } finally {
            DB::select('select release_lock(?)', [$lockName]);

        }

        $this->commit();
    }

    /**
     * 一時保存
     *
     * @param $companyId
     * @param $claimMonth
     * @param $updateData
     * @throws Exception
     */
    public function claimTempSave($companyId, $claimMonth, $updateData)
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

        $this->begin();

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
                        'memo' => $updateData['memo'],
                        'updateDatetime' => $now,
                    ]);

                }else{
                    //既存データなし
                    $ins = DB::table($this->table);
                    $ins->insert([
                        'companyId' => $companyId,
                        'claimMonth' => $strClaimMonth,
                        'claimStatus' => self::CLAIM_STATUS_UNDONE,
                        'paymentStatus' => self::PAYMENT_STATUS_UNDONE,
                        'memo' => $updateData['memo'],
                        'createDatetime' => $now,
                        'updateDatetime' => $now,
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
        $webPrice = $this->getPrice($claimMonth, $claimList, $claimList->webPlanType);

        //API検索契約の請求額を取得
        $this->deposit = is_null($apiDeposit) ? 0 : $apiDeposit;
        $apiPrice = $this->getPrice($claimMonth, $claimList, $claimList->apiPlanType);

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
     * @return int $prepaidStatus
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
                    if($claimData[0]->apiContractTypeId === self::TYPE_ALL_DEPOSIT){
                        $prepaidCharge = $claimData[0]->items['api']['id']['price'] + $claimData[0]->items['api']['deposit']['price'];
                    }elseif($claimData[0]->apiContractTypeId === self::TYPE_ID_DEPOSIT){
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

    /**
     * 請求番号が発行されているデータか
     *
     * @param $companyId
     * @param $strClaimMonth
     * @return bool
     */
    public function isClaimNo($companyId, $strClaimMonth)
    {
        $query = DB::table($this->table);
        $query->select('claimNo');
        $query->where('companyId', $companyId);
        $query->where('claimMonth', $strClaimMonth);
        $claimNo = $query->first();

        // 請求データが存在しない場合
        if( is_null($claimNo) ){
            return false;
        }

        // 対象データの請求番号がない場合
        if( is_null($claimNo->claimNo) ){
            return false;
        }

        return true;
    }
}
