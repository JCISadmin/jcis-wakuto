<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 契約プラン
 */
class TContractPlan extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tContractPlan';

    /**
     * 契約プランが指定typeのレコードを取得
     *
     * @param $companyId
     * @param $type
     * @return $data
     */
    public function getPlan($companyId, $type) {
        $model = new MUserDetail();
        $data = [];
        $query = DB::table($this->table);
        $query->select(
            'mContractPlan.contractPlanId',
            'mContractPlan.name as contractPlanName',
            'mContractPlan.planType',
            'mContractPlan.idPrice',
            'mContractPlan.unitPrice',
            'mContractType.contractTypeId',
            'mContractType.name as contractTypeName',
            'tContractPlan.startTrial',
            'tContractPlan.useStartDate',
            'tContractPlan.useUpdateDate',
            'tContractPlan.useEndAlertDate',
            'tContractPlan.useEndDate',
            'tContractPlan.idUnitPrice',
            'tContractPlan.searchUnitPrice',
            'tContractPlan.searchCount',
            'tContractPlan.deposit',
        );
        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->join('mContractType', function ($join) {
            $join->on('tContractPlan.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $query->where('mContractPlan.planType', $type);
        $query->where('tContractPlan.companyId', $companyId);
        $data = $query->first();
        $data->userDetail = $model->getDetail($companyId, $data->contractPlanId);

        return (array)$data;
    }
}
