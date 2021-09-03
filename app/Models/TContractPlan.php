<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

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
     * @return array|null
     */
    public function getPlan($companyId, $type): ?array
    {
        $model = new MUserDetail();
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

        $data = (array)$query->first();
        if (empty($data)) {
            return null;
        }

        $data['userDetail'] = $model->getDetail($companyId, $data['contractPlanId']);
        $data['ids'] = $this->countIds($data['userDetail']);

        return $data;
    }

    /**
     * 有効なID個数をカウント
     * 
     * @param $data
     * @return $count
     */
    public function countIds($data){
        $ids = 0;
        foreach( $data as $item ){
            if( $item['delFlg'] === 0){
                $ids ++;
            }
        }

        return $ids;
    }

    /**
     * データ更新
     *
     * @param $data
     */
    public function updatePlan($data, $type){
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('companyId', $data['userCompany']['companyId']);
        $query->where('mContractPlan.planType', $type);

        $query->update([
            'tContractPlan.companyId' => $data['userCompany']['companyId'],
            'tContractPlan.contractPlanId' => $data[$type]['contractPlanId'],
            'tContractPlan.contractTypeId' => $data[$type]['contractTypeId'],            
            'tContractPlan.startTrial' => $data[$type]['startTrial'],
            'tContractPlan.useStartDate' => $data[$type]['useStartDate'],
            'tContractPlan.useUpdateDate' => $data[$type]['useUpdateDate'],
            'tContractPlan.useEndAlertDate' => $data[$type]['useEndAlertDate'],
            'tContractPlan.useEndDate' => $data[$type]['useEndDate'],
            'tContractPlan.idUnitPrice' => $data[$type]['idUnitPrice'],
            'tContractPlan.searchUnitPrice' => $data[$type]['searchUnitPrice'],
            'tContractPlan.searchCount' => $data[$type]['searchCount'],
            'tContractPlan.deposit' => $data[$type]['deposit'],
            'tContractPlan.updateDatetime' => $now,
        ]);
    }
    
    /**
     * データ削除
     *
     * @param $data
     */
    public function deletePlan($data, $type){
        $query = DB::table($this->table);
        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('companyId', $data['userCompany']['companyId']);
        $query->where('mContractPlan.planType', $type);
        $query->delete();
    }
}
