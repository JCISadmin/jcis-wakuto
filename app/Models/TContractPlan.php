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
    public function getPlan($companyId, $type, $seqNo = ''): ?array
    {
        $model = new MUserDetail();
        $contractDetail = new TContractPlanDetail();
        $query = DB::table($this->table);

        $query->select(
            'mContractPlan.contractPlanId',
            'mContractPlan.name as contractPlanName',
            'mContractPlan.planType',
            'mContractPlan.idPrice',
            'mContractPlan.unitPrice',
            'tContractPlan.startTrial',
            'tContractPlan.useStartDate',
            'tContractPlan.useUpdateDate',
            'tContractPlan.useEndAlertDate',
            'tContractPlan.useEndDate',
            'tContractPlan.deposit',
        );

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->where('mContractPlan.planType', $type);
        $query->where('tContractPlan.companyId', $companyId);

        $data = (array)$query->first();
        if (empty($data)) {
            return null;
        }

        $data['contractDetail'] = $contractDetail->getDetail($companyId, $type, $seqNo);
        //契約情報履歴から取得できない場合、nullを返す
        if(is_null($data['contractDetail'])){
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
     * @return int
     */
    public function countIds($data): int
    {
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
     * @param $type
     */
    public function updatePlan($data, $type, $seqNo, $contractUpdFlg = false) {

        //既存データ件数をカウント
        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));
        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('companyId', $data['userCompany']['companyId']);
        $query->where('mContractPlan.planType', $type);
        $count = $query->first();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $contractDetail = new TContractPlanDetail();

        if($count->count > 0){
            //既存データありの場合
            $updQuery = DB::table($this->table);
            $updQuery->join('mContractPlan', function ($join) {
                $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $updQuery->where('companyId', $data['userCompany']['companyId']);
            $updQuery->where('mContractPlan.planType', $type);

            $updQuery->update([
                'tContractPlan.companyId' => $data['userCompany']['companyId'],
                'tContractPlan.contractPlanId' => $data[$type]['contractPlanId'],
                'tContractPlan.startTrial' => $data[$type]['startTrial'],
                'tContractPlan.useStartDate' => $data[$type]['useStartDate'],
                'tContractPlan.useUpdateDate' => $data[$type]['useUpdateDate'],
                'tContractPlan.useEndAlertDate' => $data[$type]['useEndAlertDate'],
                'tContractPlan.useEndDate' => $data[$type]['useEndDate'],
                'tContractPlan.deposit' => $data[$type]['deposit'],
                'tContractPlan.updateDatetime' => $now,
            ]);

            $contractDetail->updatePlan($data, $type, $seqNo, $contractUpdFlg);

        }else{
            //既存データなしの場合
            $insQuery = DB::table($this->table);
            $insQuery->join('mContractPlan', function ($join) {
                $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $insQuery->where('companyId', $data['userCompany']['companyId']);
            $insQuery->where('mContractPlan.planType', $type);

            $insQuery->insert([
                'tContractPlan.companyId' => $data['userCompany']['companyId'],
                'tContractPlan.contractPlanId' => $data[$type]['contractPlanId'],
                'tContractPlan.startTrial' => $data[$type]['startTrial'],
                'tContractPlan.useStartDate' => $data[$type]['useStartDate'],
                'tContractPlan.useUpdateDate' => $data[$type]['useUpdateDate'],
                'tContractPlan.useEndAlertDate' => $data[$type]['useEndAlertDate'],
                'tContractPlan.useEndDate' => $data[$type]['useEndDate'],
                'tContractPlan.deposit' => $data[$type]['deposit'],
                'tContractPlan.createDatetime' => $now,
                'tContractPlan.updateDatetime' => $now,
            ]);

            $contractDetail->updatePlan($data, $type, $seqNo, $contractUpdFlg);
        }
    }

    /**
     * データ削除
     *
     * @param $data
     * @param $type
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

    /**
     * データ追加
     *
     * @param $data
     * @param $type
     */
    public function insertPlan($data, $type) {
        $dt = new Datetime();
        $contractDetail = new TContractPlanDetail();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->insert([
            'tContractPlan.companyId' => $data['userCompany']['companyId'],
            'tContractPlan.contractPlanId' => $data[$type]['contractPlanId'],
            'tContractPlan.startTrial' => $data[$type]['startTrial'],
            'tContractPlan.useStartDate' => $data[$type]['useStartDate'],
            'tContractPlan.useUpdateDate' => $data[$type]['useUpdateDate'],
            'tContractPlan.useEndAlertDate' => $data[$type]['useEndAlertDate'],
            'tContractPlan.useEndDate' => $data[$type]['useEndDate'],
            'tContractPlan.deposit' => $data[$type]['deposit'],
            'tContractPlan.createDatetime' => $now,
            'tContractPlan.updateDatetime' => $now,
        ]);

        $contractDetail->insertPlan($data, $type);
    }

    /**
     * デポジット減算処理
     *
     * @param $companyId
     * @param $contractPlanId
     */
    public function useDeposit($companyId, $contractPlanId)
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);

        /** @var object $planData */
        $planData = $query->lockForUpdate()->first();

        if ($planData->contractTypeId !== self::DEPOSIT_USE_PLAN_TYPE) {
            return;
        }

        $dt = new Datetime();
        if ($dt->format('Y-m-d') < $planData->useStartDate) {
            return;
        }

        $deposit = $planData->deposit - $planData->searchUnitPrice;

        //デポジット残高の減算結果が0以下の場合、0で更新
        if($deposit < 0){
            $deposit = 0;
        }

        $updQuery = DB::table($this->table);
        $updQuery->where('companyId', $companyId);
        $updQuery->where('contractPlanId', $contractPlanId);
        $updQuery->update(['deposit' => $deposit]);

    }

    /**
     * デポジット残高チェック
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $count
     * @return bool true:デポジット残高あり
     */
    public function checkDeposit($companyId, $contractPlanId, $count): bool
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);

        /** @var object $planData */
        $planData = $query->lockForUpdate()->first();

        if ($planData->contractTypeId !== self::DEPOSIT_USE_PLAN_TYPE) {
            return true;
        }

        $deposit = $planData->deposit - ($planData->searchUnitPrice * $count);
        if ($deposit < 0) {
            return false;
        }

        return true;

    }

    /**
     * 会社ID・契約プランIDを指定してレコードを取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @return Object|null
     */
    public function getPlanUsePlanId($companyId, $contractPlanId): Object|null
    {
        $query = DB::table($this->table);
        $query->select('*');
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);

        $data = $query->first();

        return $data;
    }
}
