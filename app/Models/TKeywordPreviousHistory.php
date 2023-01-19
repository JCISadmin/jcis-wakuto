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

    /**
     * 指定期間の検索件数を取得
     *
     * @param $companyId
     * @param $type
     * @param $userId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getSearchCount($companyId, $type, $userId, $startDate, $endDate): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countSearch'));
        $query->where('companyId', $companyId);
        $query->join('mContractPlan', function ($join) {
            $join->on('tKeywordPreviousHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $query->where('mContractPlan.planType', $type); 

        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();
        return $count->countSearch;
    }

    
    /**
     * 指定期間の課金検索数を取得
     *
     * @param $companyId
     * @param $type
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getChargeSearchCount($companyId, $type, $startDate, $endDate): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countChargeSearch'));
        $query->where('companyId', $companyId);
        $query->join('mContractPlan', function ($join) {
            $join->on('tKeywordPreviousHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('mContractPlan.planType', $type); 
        $query->where('chargeFlg', self::CHARGE_FLG_ON);

        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();

        return $count->countChargeSearch;
    }

}
