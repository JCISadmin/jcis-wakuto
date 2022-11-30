<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Support\Collection;

/**
 * 検索
 */
class TAcurisKeywordHistory extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tAcurisKeywordHistory';

    /**
     * 検索キーワード履歴登録
     *
     * @param $companyId
     * @param $userId
     * @param $detailFlg
     * @param $chargeFlg
     * @throws Exception
     */
    public function ins($companyId, $userId, $detailFlg, $chargeFlg)
    {

        if ($companyId == 'admin') {
            return;
        }

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $searchCount = 0;
        $lookupCount = 0;
        $errSearchCount = 0;
        $errLookupCount = 0;

        // 検索種別
        switch($chargeFlg){
            case SELF::CHARGE_FLG_ON:
                // 一覧検索 or 詳細検索
                if($detailFlg === SELF::DETAIL_FLG_OFF){
                    $searchCount += 1;
                    $updateColumn = 'searchCount';
                }else{
                    $lookupCount += 1;
                    $updateColumn = 'lookupCount';
                }
                break;
            case SELF::CHARGE_FLG_OFF:
                // 一覧検索 or 詳細検索
                if($detailFlg === SELF::DETAIL_FLG_OFF){
                    $errSearchCount += 1;
                    $updateColumn = 'errSearchCount';
                }else{
                    $errLookupCount += 1;
                    $updateColumn = 'errLookupCount';
                }
                break;
        }

        // 既存データ取得
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $query->where('searchDate', $now);
        $count = $query->first();

        if ($count->count > 0) {
            //既存データあり
            $upd = DB::table($this->table);
            $upd->where('companyId', $companyId);
            $query->where('userId', $userId);
            $upd->where('searchDate', $now);
            $upd->increment($updateColumn);

        } else {
            //既存データなし
            //'searchCount'を1に設定
            $ins = DB::table($this->table);
            $ins->insert([
                'companyId' => $companyId,
                'userId' => $userId,
                'searchDate' => $now,
                'searchCount' => $searchCount,
                'lookupCount' => $lookupCount,
                'errSearchCount' => $errSearchCount,
                'errLookupCount' => $errLookupCount,
            ]);
        }
    }

    /**
     * 指定期間検索件数(会社別)を取得
     *
     * @param $companyId
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getSearchDataByCompanyId($companyId, $startDate, $endDate): Collection
    {

        $query = DB::table($this->table);
        $query->select(
            'companyId',
            DB::raw('IFNULL( SUM(searchCount) , 0) as searchCount'),
            DB::raw('IFNULL( SUM(lookupCount) , 0) as lookupCount'),
            DB::raw('IFNULL( SUM(errSearchCount) , 0) as errSearchCount'),
            DB::raw('IFNULL( SUM(errLookupCount) , 0) as errLookupCount'),
        );
        $query->where('companyId',$companyId);
        $query->whereBetween('searchDate',[$startDate, $endDate]);

        $query->groupBy([
            'companyId',
        ]);

        return $query->get();
    }

    /**
     * 指定期間検索件数(ユーザ別)を取得
     *
     * @param $companyId
     * @param $startDate
     * @param $endDate
     * @return Collection
     */
    public function getMonthSearchDataByUserId($companyId, $startDate, $endDate): Collection
    {

        $query = DB::table($this->table);
        $query->select(
            'companyId',
            'userId',
            DB::raw('IFNULL( SUM(searchCount) , 0) as searchCount'),
            DB::raw('IFNULL( SUM(lookupCount) , 0) as lookupCount'),
            DB::raw('IFNULL( SUM(errSearchCount) , 0) as errSearchCount'),
            DB::raw('IFNULL( SUM(errLookupCount) , 0) as errLookupCount'),
        );
        $query->where('companyId',$companyId);
        if(!is_null($startDate) && !is_null($endDate)){
            $query->whereBetween('searchDate',[$startDate, $endDate]);
        }

        $query->groupBy([
            'companyId',
            'userId'
        ]);

        return $query->get();
    }

}