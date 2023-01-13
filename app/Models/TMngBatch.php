<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * バッチ管理テーブル
 */
class TMngBatch extends baseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tMngBatch';


    /**
     * 新規追加
     * 
     * @param $companyId
     * @param $batchId
     * @param $searchType
     * @param $searchCondition
     */
    public function ins($companyId, $batchId, $searchType, $searchCondition)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d H:i:s');

        $insData = [
            'companyId' => $companyId,
            'batchId' => $batchId,
            'searchType' => $searchType,
            'searchCondition' => $searchCondition,
            'result' => self::BATCH_UNDONE,
            'errorCode' =>'',
            'fileName' => $companyId . $batchId,
            'createDatetime' =>$now,
            'updateDatetime' =>$now,
        ];

        DB::table($this->table)->insert($insData);

    }

    /**
     * バッチ状態更新
     *
     * @param $companyId
     * @param $batchId
     * @param $result
     * @param $errorCode
     */
    public function updStatus($companyId, $batchId, $result, $errorCode)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d H:i:s');

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);
        $query->update([
            'result' => $result,
            'errorCode' => $errorCode,
            'updateDatetime' => $now
        ]);

    }

    /**
     * データ取得
     *
     * @param $companyId
     * @param $batchId
     * @return array|null
     */
    public function get($companyId, $batchId): array|null
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);

        $ret =  $query->first();
        if (is_null($ret)) {
            return null;
        } else {
            return (array)$ret;
        }

    }

    /**
     * 論理削除
     * 
     * @param $companyId
     * @param $batchId
     * @return void
     */
    public function softDelete($companyId, $batchId)
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);

        $query->update([
            'result' => self::BATCH_DELETE,
            'delFlg' => true
        ]);
    }

}
