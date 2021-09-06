<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\TKeywordHistory;

/**
 * 利用明細
 */
class UseReport extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = '';

    /**
     * 利用情報を取得
     *
     * @param $companyId
     * @param $userId
     * @return array|null
     */
    public function getList($companyId, $userId): ?array
    {
        $query = DB::table('tContractPlan');

        $query->select(
            'tContractPlan.contractPlanId',
            'tContractPlan.useUpdateDate',
            'tContractPlan.searchUnitPrice',
            'tContractPlan.deposit',
        );

        $query->join('mUserDetail', function ($join) {
            $join->on('tContractPlan.companyId', '=', 'mUserDetail.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'mUserDetail.contractPlanId');
        });

        $query->where('mUserDetail.companyId', $companyId);
        $query->where('mUserDetail.userId', $userId);

        $data = $query->first();

        $model = new TKeywordHistory();
        $monthlySearchCount = $model->countMonthlySearch($companyId, $userId, $data);
        $yearlySearchCount = $model->countYearlySearch($companyId, $userId, $data);
        $depositBalance = $data->deposit - $data->searchUnitPrice * $yearlySearchCount;

        $list = [
            'monthlySearchCount' => $monthlySearchCount,
            'yearlySearchCount' => $yearlySearchCount,
            'depositBalance' => $depositBalance,
    
        ];

        return $list;
    }
    
    
}
