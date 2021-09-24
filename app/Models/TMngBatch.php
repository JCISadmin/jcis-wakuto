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
     * @param $companyId
     * @param $batchId
     * @param $searchCondition
     */
    public function ins($companyId, $batchId, $searchCondition)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $insData = [
            'companyId' => $companyId,
            'batchId' => $batchId,
            'searchCondition' => $searchCondition,
            'result' =>'未実行',
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
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);
        $query->update([
            'result' => $result,
            'errorCode' => $errorCode
        ]);

    }


}
