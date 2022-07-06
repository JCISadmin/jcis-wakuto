<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;

/**
 * 検索
 */
class TKeywordHistory extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tKeywordHistory';

    /**
     * 月間検索件数を取得
     *
     * @param $companyId
     * @param $userId
     * @param $contractPlanId
     * @param $year
     * @param $month
     * @return mixed
     */
    public function getMonthSearchCount($companyId, $userId, $contractPlanId, $year, $month): mixed
    {
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearchMonth'));
        $query->where('companyId', $companyId);
        $query->whereYear('searchDate', $year);
        $query->whereMonth('searchDate', $month);
        if(is_null($contractPlanId) === false){
            $query->where('contractPlanId', $contractPlanId);
        }
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $count = $query->first();

        return $count->countSearchMonth;
    }

    /**
     * 年間検索件数を取得
     *
     * @param $companyId
     * @param $userId
     * @param $contractPlanId
     * @param $date
     * @return mixed
     */
    public function getYearSearchCount($companyId, $userId, $contractPlanId, $date): mixed
    {

        $startDate = $date;
        $thisYear = mb_substr($startDate, 0, 4);
        $nextYear = (int)$thisYear + 1;
        $endDate = str_replace($thisYear, $nextYear, $startDate);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearchYear'));
        $query->where('companyId', $companyId);
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $query->where('contractPlanId', $contractPlanId);
        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();

        return $count->countSearchYear;
    }

    /**
     * 検索キーワード履歴登録
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $keywordHash
     * @throws Exception
     */
    public function ins($companyId, $contractPlanId, $userId, $keywordHash)
    {

        if ($companyId == 'admin') {
            return;
        }

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $model = new TContractPlan();
        $detailModel = new TContractPlanDetail();

        //課金フラグを設定
        $plan = $model->getPlanUsePlanId($companyId, $contractPlanId);
        $detailPlan = $detailModel->getPlanUsePlanId($companyId, $contractPlanId);

        $chargeFlg = self::CHARGE_FLG_OFF;
        if(is_null($plan->useStartDate) === false){
            if($detailPlan->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){
                //全額デポジットの場合
                if($now >= $plan->useStartDate && $plan->deposit == 0){
                    //本契約中、かつ検索時のデポジット残高が0の場合に、課金フラグをON
                    $chargeFlg = $this::CHARGE_FLG_ON;
                }
            }
        }

        $this->begin();

        try {

            $query = DB::table($this->table);
            $query->insert([
                'companyId' => $companyId,
                'contractPlanId' => $contractPlanId,
                'userId' => $userId,
                'hash' => $keywordHash,
                'keyword' => $keywordHash,
                'searchDate' => $now,
                'chargeFlg' => $chargeFlg,
            ]);

            // デポジット減算
            $model->useDeposit($companyId, $contractPlanId);

            $this->commit();

        } catch (QueryException $e) {
            $this->rollback();

            // Duplicate error　は無視する。
            if ($e->getCode() != '23000') {
                throw $e;
            }
        }

    }

    /**
     * 指定期間の検索件数を取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $startDate
     * @param $endDate
     * @param null $trialPlanId
     * @return mixed
     */
    public function getSearchCount($companyId, $contractPlanId, $userId, $startDate, $endDate, $trialPlanId = null): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearch'));
        $query->where('companyId', $companyId);
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        if (is_null($trialPlanId)) {
            $query->where('contractPlanId', $contractPlanId);
        } else {
            $query->whereIn('contractPlanId', [$contractPlanId, $trialPlanId]);
        }
        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();
        return $count->countSearch;
    }

    /**
     * 指定期間の課金検索数を取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getChargeSearchCount($companyId, $contractPlanId, $startDate, $endDate): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countChargeSearch'));
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('chargeFlg', self::CHARGE_FLG_ON);
        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();

        return $count->countChargeSearch;
    }

    /**
     * 指定期間の検索数を取得(レポート機能用)
     *
     * @param $companyId
     * @param $userIds
     * @param $type
     * @param $startDate
     * @param $endDate
     * @param $trialFlg
     * @return mixed
     */
    public function getSearchCountByReport($companyId, $userIds, $type, $startDate, $endDate, $trialFlg = false): mixed
    {
        $retAry = [];

        foreach($userIds as $userId){
            $query = DB::table($this->table);
            $query->select(
                'tKeywordHistory.userId',
                'mUserDetail.name',
                'tKeywordHistory.chargeFlg',
                DB::raw('count(*) as searchCount',
            ));
            $query->leftJoin('mContractPlan', function ($join) {
                $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $query->leftJoin('mUserDetail', function ($join) {
                $join->on('tKeywordHistory.userId', '=', 'mUserDetail.userId');
            });

            $query->where('tKeywordHistory.companyId', $companyId);
            $query->where('tKeywordHistory.userId', $userId->userId);
            $query->where('mContractPlan.planType', $type);
            $query->whereBetween('searchDate', [$startDate, $endDate]);
            $query->groupBy([
                'tKeywordHistory.userId',
                'mUserDetail.name',
                'tKeywordHistory.chargeFlg',
            ]);
        
            $list = $query->get();

            //取得データが無い場合 空データを生成
            if($list->isEmpty()){
                //トライアルの場合 データ生成なし
                if($trialFlg){
                    return null;
                }

                $retAry[] = [
                    'userId' => $userId->userId,
                    'name' => $userId->name,
                    'chargeFlg' => 0,
                    'searchCount' => 0,
                ];
            }else{

                foreach($list as $item){
                    $retAry[] = [
                        'userId' => $userId->userId,
                        'name' => $userId->name,
                        'chargeFlg' => $item->chargeFlg,
                        'searchCount' => $item->searchCount,
                    ];
                }
            }

        }

        return $retAry;
    }

    /**
     * 月別検索件数を取得
     *
     * @param $companyId
     * @param $userId
     * @return Collection
     */
    public function getSearchCountByMonth($companyId, $userId): Collection
    {

        $subQuery = DB::table($this->table);
        $subQuery->select(
            'companyId',
            'userId',
            'contractPlanId',
            'hash',
            DB::raw('date_format(searchDate,"%Y-%m") as searchMonth'),
            DB::raw('1 as cnt')
        );
        $subQuery->where('companyId',$companyId);
        $subQuery->where('userId',$userId);

        $query = DB::table($this->table);
        $query->select(
            'subKwh.companyId',
            'subKwh.userId',
            'searchMonth',
            DB::raw('sum(cnt) as MonthlySearchCount')
        );
        $query->joinSub($subQuery, 'subKwh', function($join){
            $join->on('tKeywordHistory.companyId', '=', 'subKwh.companyId');
            $join->on('tKeywordHistory.userId', '=', 'subKwh.userId');
            $join->on('tKeywordHistory.contractPlanId', '=', 'subKwh.contractPlanId');
            $join->on('tKeywordHistory.hash', '=', 'subKwh.hash');
        });
        $query->groupBy('searchMonth');

        return $query->get();
    }


}