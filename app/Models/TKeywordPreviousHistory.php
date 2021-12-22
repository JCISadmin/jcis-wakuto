<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class TKeywordPreviousHistory extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tKeywordPreviousHistory';

    /**
     * インサート処理
     * 
     * @param $data
     */
    public function ins($data) {

        // seq_no取得
        $maxQuery = DB::table($this->table);

        $maxQuery->where('companyId', $data->companyId);
        $maxQuery->where('contractPlanId', $data->contractPlanId);
        $maxQuery->where('userId', $data->userId);
        $maxQuery->where('hash', $data->hash);

        $nextSeqNo = is_null($maxQuery->max('seqNo')) ? 1 : $maxQuery->max('seqNo') + 1;

        // insert処理
        $query = DB::table($this->table);
        $query->insert([
            'companyId' => $data->companyId,
            'contractPlanId' => $data->contractPlanId,
            'userId' => $data->userId,
            'hash' => $data->hash,
            'seqNo' => $nextSeqNo,
            'keyword' => $data->keyword,
            'searchDate' => $data->searchDate,
            'chargeFlg' => $data->chargeFlg,
        ]);
    }
}
