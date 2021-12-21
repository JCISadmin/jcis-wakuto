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

        // １年以内に検索されているか
        $isSearchedYear = $this->checkSearchedYear($companyId, $contractPlanId, $userId, $keywordHash, $now);
        if ($isSearchedYear) {
            return;
        }

        //課金フラグを設定
        $plan = $model->getPlanUsePlanId($companyId, $contractPlanId);
        $chargeFlg = self::CHARGE_FLG_OFF;
        if(is_null($plan->useStartDate) === false){
            if($plan->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){
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


    /**
     * 過去１年間で同一ワードで検索されたか
     * 
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $keywordHash
     * @return bool
     */
    private function checkSearchedYear($companyId, $contractPlanId, $userId, $keywordHash, $now) {

        $query = DB::table($this->table);

        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);
        $query->where('hash', $keywordHash);

        $data = $query->get();

        // 過去に検索されていない場合
        if (count($data) < 1) {
            return false;
        }

        // １年以上前の場合
        $searchDate = new Datetime($data[0]->searchDatetime);
        $searchDate->modify('+1 year');
        if ($searchDate < $now) {

            // 該当検索キーワードのSearchDate更新
            $updateQuery = DB::table($this->table);

            $updateQuery->where('companyId', $companyId);
            $updateQuery->where('contractPlanId', $contractPlanId);
            $updateQuery->where('userId', $userId);
            $updateQuery->where('hash', $keywordHash);

            $updateQuery->update(['searchDate', $now]);

            return false;
        }

        // １年以内の場合
        return true;
    }
}