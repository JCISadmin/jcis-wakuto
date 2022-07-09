<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Datetime;

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
     * @throws Exception
     */
    public function ins($companyId)
    {
        //'searchMonth'を作成
        $now = new DateTime();
        $searchMonth = $now->format('Y-m');
        $strSearchMonth = str_replace('-', '', $searchMonth);

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('searchMonth', $strSearchMonth);
        $count = $query->first();
        $calCount = DB::table($this->table)->where([
            'companyId' => $companyId,
            'searchMonth' => $strSearchMonth,
        ])->get();


        if ($count->count > 0) {
            //既存データあり
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $upd->where('searchMonth', $strSearchMonth);
            $upd->update([ 
                'searchCount' => $calCount[0]->searchCount + 1,
            ]);

        } else {
            //既存データなし
            //'searchCount'を1に設定
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'searchMonth' => $strSearchMonth,
                'searchCount' => 1,
            ]);
        }
    }

    /**
     * 同一ワード検索数取得
     *
     * @param $companyId
     * @param $userId
     * @throws Exception
     */
    public function get($companyId,$searchMonth)
    {
        $strSearchMonth = str_replace('-', '', $searchMonth);

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('searchMonth', $strSearchMonth);
        $list = $query->first();

        if(is_null($list)){
            return 0;
        }

        return $list->searchCount;
    }

}
