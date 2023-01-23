<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class TKeywordHistoryDetail extends Model
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tKeywordHistoryDetail';

    /**
     * 同一キーワード検索回数の登録
     *
     * @param $companyId
     * @param $userId
     * @param $searchDate
     * @throws Exception
     */
    public function ins($companyId, $userId, $searchDate)
    {
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $query->where('searchDate', $searchDate);
        $count = $query->first();
        $calCount = DB::table($this->table)->where([
            'companyId' => $companyId,
            'userId' => $userId,
            'searchDate' => $searchDate,
        ])->get();


        if ($count->count > 0) {
            //既存データあり
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $query->where('userId', $userId);
            $upd->where('searchDate', $searchDate);
            $upd->update([ 
                'searchCount' => $calCount[0]->searchCount + 1,
            ]);

        } else {
            //既存データなし
            //'searchCount'を1に設定
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'userId' => $userId,
                'searchDate' => $searchDate,
                'searchCount' => 1,
            ]);
        }
    }

    /**
     * 同一ワード検索数取得
     *
     * @param $companyId
     * @param $userId
     * @param $startDate
     * @param $endDate
     * @throws Exception
     */
    public function getSearchCount($companyId, $userId, $startDate, $endDate)
    {
        $query = DB::table($this->table);
        $query->select(
            'companyId',
            'userId',
            DB::raw('sum(searchCount) as count'),
        );
        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $query->groupBy([
            'companyId',
            'userId',
        ]);
        $list = $query->first();

        if(is_null($list)){
            return 0;
        }

        return $list->count;
    }

}
