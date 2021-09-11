<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Database\QueryException;

/**
 * 契約プラン
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
     * 今月検索件数を取得
     *
     * @param $companyId
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function getMonthSearchCount($companyId, $userId, $data): mixed
    {
        $dt = new Datetime();
        $year = $dt->format('Y');
        $month = $dt->format('m');

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearchMonth'));
        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $query->where('contractPlanId', $data->contractPlanId);
        $query->whereYear('searchDate', $year);
        $query->whereMonth('searchDate', $month);
        $count = $query->first();

        return $count->countSearchMonth;
    }

    /**
     * 年間検索件数を取得
     *
     * @param $companyId
     * @param $userId
     * @param $data
     * @return mixed
     */
    public function getYearSearchCount($companyId, $userId, $data): mixed
    {

        $startDate = $data->useUpdateDate;
        $thisYear = mb_substr($startDate, 0, 4);
        $nextYear = (int)$thisYear + 1;
        $endDate = str_replace($thisYear, $nextYear, $startDate);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearchYear'));
        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $query->where('contractPlanId', $data->contractPlanId);
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
     */
    public function ins($companyId, $contractPlanId, $userId, $keywordHash)
    {

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

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

        } catch (QueryException $e) {
            // Duplicate error　は無視する。
            if ($e->getCode() != '23000') {
                throw $e;
            }
        }


    }



}
