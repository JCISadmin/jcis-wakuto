<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Database\QueryException;

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

        $this->begin();

        try {

            $query = DB::table($this->table);
            $query->insert([
                'companyId' => $companyId,
                'contractPlanId' => $contractPlanId,
                'userId' => $userId,
                'hash' => $keywordHash,
                'keyword' => $keywordHash,
                'searchDate' => $now
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
     * @return mixed
     */
    public function getSearchCount($companyId, $contractPlanId, $userId, $startDate, $endDate): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearch'));
        $query->where('companyId', $companyId);
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $query->where('contractPlanId', $contractPlanId);
        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();
        return $count->countSearch;
    }
}