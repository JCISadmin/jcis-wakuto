<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

/**
 * 契約プラン
 */
class TContractPlanDetail extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tContractPlanDetail';

    /**
     * 一覧を取得
     *
     * @param $companyId
     * @return $list
     */
    public function getList($companyId)
    {
        //WEBプラン
        $webPlan = DB::table('tContractPlanDetail');
        $webPlan->select(
            'tContractPlanDetail.*',
            'mContractPlan.name as planName',
            'mContractPlan.planType',
            'mContractType.name as typeName',
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->join('mContractType', function ($join) {
            $join->on('tContractPlanDetail.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $webPlan->where('tContractPlanDetail.companyId', $companyId);
        $webPlan->where('mContractPlan.planType', self::PLAN_TYPE_WEB);

        //APIプラン
        $apiPlan = DB::table('tContractPlanDetail');
        $apiPlan->select(
            'tContractPlanDetail.*',
            'mContractPlan.name as planName',
            'mContractPlan.planType',
            'mContractType.name as typeName',
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->join('mContractType', function ($join) {
            $join->on('tContractPlanDetail.contractTypeId', '=', 'mContractType.contractTypeId');
        });
        $apiPlan->where('tContractPlanDetail.companyId', $companyId);
        $apiPlan->where('mContractPlan.planType', self::PLAN_TYPE_API);

        $detail = DB::table($this->table);
        $detail->select(
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.seqNo',
        );
        $detail->groupBy([
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.seqNo'
        ]);

        $query = DB::table($detail,'detail');
        $query->select(
            'detail.companyId',
            'detail.seqNo',
            'webPlan.contractStartDate as webPlanContractStartDate',
            'webPlan.contractEndDate as webPlanContractEndDate',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.planName as webPlanName',
            'webPlan.contractTypeId as webPlanTypeId',
            'webPlan.typeName as webTypeName',
            'webPlan.idUnitPrice as webPlanIdUnitPrice',
            'webPlan.searchUnitPrice as webPlanSearchUnitPrice',
            'webPlan.searchCount as webPlanSearchCount',
            'apiPlan.contractStartDate as apiPlanContractStartDate',
            'apiPlan.contractEndDate as apiPlanContractEndDate',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.planName as apiPlanName',
            'apiPlan.contractTypeId as apiPlanTypeId',
            'apiPlan.typeName as apiTypeName',
            'apiPlan.idUnitPrice as apiPlanIdUnitPrice',
            'apiPlan.searchUnitPrice as apiPlanSearchUnitPrice',
            'apiPlan.searchCount as apiPlanSearchCount',
        );

        $query->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('detail.companyId', '=', 'webPlan.companyId');
            $join->on('detail.seqNo', '=', 'webPlan.seqNo');
        });

        $query->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('detail.companyId', '=', 'apiPlan.companyId');
            $join->on('detail.seqNo', '=', 'apiPlan.seqNo');
        });
        $query->where('detail.companyId', $companyId);

        $list = $query->get();

        return $list;
    }

    /**
     * 一覧を取得
     *
     * @param $companyId
     * @param $type
     * @param $seqNo
     * @return $data
     */
    public function getDetail($companyId, $type, $seqNo = '')
    {
        $query = DB::table($this->table);

        $query->select(
            'mContractPlan.contractPlanId',
            'mContractPlan.name as contractPlanName',
            'mContractPlan.planType',
            'mContractPlan.idPrice',
            'mContractPlan.unitPrice',
            'mContractType.contractTypeId',
            'mContractType.name as contractTypeName',
            'tContractPlanDetail.seqNo',
            'tContractPlanDetail.idUnitPrice',
            'tContractPlanDetail.searchUnitPrice',
            'tContractPlanDetail.searchCount',
        );

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->join('mContractType', function ($join) {
            $join->on('tContractPlanDetail.contractTypeId', '=', 'mContractType.contractTypeId');
        });

        $query->where('mContractPlan.planType', $type);
        $query->where('tContractPlanDetail.companyId', $companyId);

        if($seqNo === ''){
            //seqNo最大(最新の変更)データを取得
            $query->where('tContractPlanDetail.seqNo', $query->max('seqNo'));
        }else{
            $query->where('tContractPlanDetail.seqNo', $seqNo);
        }

        $data = (array)$query->first();
        
        if (empty($data)) {
            return null;
        }

        return $data;
    }

    /**
     * データ追加
     *
     * @param $data
     * @param $type
     */
    public function insertPlan($data, $type) {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->insert([
            'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
            'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
            //新規追加はseqNo=1
            'tContractPlanDetail.seqNo' => 1,
            'tContractPlanDetail.contractStartDate' => $data[$type]['useStartDate'],
            'tContractPlanDetail.contractEndDate' => $data[$type]['useEndDate'],
            'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
            'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
            'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
            'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
            'tContractPlanDetail.createDatetime' => $now,
            'tContractPlanDetail.updateDatetime' => $now,
        ]);
    }

    
    /**
     * データ更新
     *
     * @param $data
     * @param $type
     * @param $seqNo
     * @param $contractUpdFlg
     */
    public function updatePlan($data, $type, $seqNo, $contractUpdFlg = false) {

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        //seqNoが指定されていない場合 新規追加
        if(is_null($seqNo)){
            $this->insertPlan($data, $type);
            return;
        }

        //契約更新(履歴追加)
        if($contractUpdFlg === true){
            $this->contractUpdatePlan($data, $type, $seqNo);
            return;
        }

        $count = $this->getDataCount($data['userCompany']['companyId'], $type);

        //既存データあり
        if($count > 0){
            //対象seqNoレコードを更新
            $updQuery = DB::table($this->table);
            $updQuery->join('mContractPlan', function ($join) {
                $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $updQuery->where('companyId', $data['userCompany']['companyId']);
            $updQuery->where('mContractPlan.planType', $type);
            $minSeqNo = $updQuery->min('seqNo');
            $maxSeqNo = $updQuery->max('seqNo');
            $updQuery->where('seqNo', $seqNo);

            //条件別更新
            if($count === 1){
                //履歴が一つしかない場合 契約開始日/終了日含め更新
                $updQuery->update([
                    'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
                    'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
                    'tContractPlanDetail.seqNo' => $seqNo,
                    'tContractPlanDetail.contractStartDate' => $data[$type]['useStartDate'],
                    'tContractPlanDetail.contractEndDate' => $data[$type]['useEndDate'],
                    'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
                    'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
                    'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
                    'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
                    'tContractPlanDetail.updateDatetime' => $now,
                ]);

            }elseif($seqNo === $maxSeqNo){
                //最新の履歴の場合 契約終了日含め更新
                $updQuery->update([
                    'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
                    'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
                    'tContractPlanDetail.seqNo' => $seqNo,
                    'tContractPlanDetail.contractEndDate' => $data[$type]['useEndDate'],
                    'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
                    'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
                    'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
                    'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
                    'tContractPlanDetail.updateDatetime' => $now,
                ]);

            }else{
                //契約開始日/終了日を除いて更新
                $updQuery->update([
                    'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
                    'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
                    'tContractPlanDetail.seqNo' => $seqNo,
                    'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
                    'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
                    'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
                    'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
                    'tContractPlanDetail.updateDatetime' => $now,
                ]);
            }
    
        //既存データ無し
        }else{
            //新規追加
            $this->insertPlan($data, $type);
        }

    }

    /**
     * 契約更新(履歴追加更新)
     *
     * @param $data
     * @param $type
     * @param $seqNo
     */
    public function contractUpdatePlan($data, $type, $seqNo) {

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $insQuery = DB::table($this->table);

        //seqNo +1して追加
        $insQuery->insert([
            'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
            'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
            'tContractPlanDetail.seqNo' => $seqNo+1,
            'tContractPlanDetail.contractStartDate' => $data['contractStartDate'],
            'tContractPlanDetail.contractEndDate' => $data[$type]['useEndDate'],
            'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
            'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
            'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
            'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
            'tContractPlanDetail.updateDatetime' => $now,
        ]);

        //直前契約の終了日を 契約開始日前日に更新
        $this->beforeContractUpdate($data, $type, $seqNo);
    }

    /**
     * 直前契約の契約終了日を更新(契約更新日の前日に設定)
     *
     * @param $data
     * @param $type
     * @param $seqNo
     */
    public function beforeContractUpdate($data, $type, $seqNo) {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->where('tContractPlanDetail.companyId', $data['userCompany']['companyId']);
        $query->where('mContractPlan.planType', $type);
        $query->where('seqNo', $seqNo);

        $count = $query->first();

        //直前の契約データあり
        if($count->count > 0){

            $dt = new DateTime($data['contractStartDate']);

            $contractEndDate = $dt->modify('-1 day');

            $updQuery = DB::table($this->table);
            
            $updQuery->join('mContractPlan', function ($join) {
                $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
    
            $updQuery->where('tContractPlanDetail.companyId', $data['userCompany']['companyId']);
            $updQuery->where('mContractPlan.planType', $type);
            $updQuery->where('seqNo', $seqNo);

            $updQuery->update([
                'tContractPlanDetail.contractEndDate' => $contractEndDate,
            ]);
        
        }
    }

    /**
     * 会社ID(&プラン)指定で最大seqNoを取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @return $seqNo
     */
    public function getMaxSeqNo($companyId, $contractPlanId = null) {
    
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        if(!is_null($contractPlanId)){
            $query->where('contractPlanId', $contractPlanId);
        }
        
        $seqNo = $query->max('seqNo');

        if($seqNo === null){
            $seqNo = '';
        }

        return $seqNo;
    }

    /**
     * 会社ID・契約プランIDを指定してレコードを取得(seqNo最新)
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
        $query->where('seqNo',$query->max('seqNo'));

        $data = $query->first();
        return $data;
    }


    /**
     * 既存データが存在するかチェック
     *
     * @param $companyId
     * @param $type
     * @return $count
     */
    public function getDataCount($companyId, $type = null) {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->where('tContractPlanDetail.companyId', $companyId);
        if(!is_null($type)){
            $query->where('mContractPlan.planType', $type);
        }
        $count = $query->first();

        return $count->count;
    }


    /**
     * データ取得(開始/終了日 指定)
     *
     * @param $companyId
     * @param $startMonth
     * @param $endMonth
     * @return $data
     */
    public function getDetailByMonth($companyId, $startMonth, $endMonth, $type = null)
    {
        $query = DB::table($this->table);
        $query->select(
            'tContractPlanDetail.*',
            'mContractPlan.planType',
        );
        $query->leftJoin('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('companyId', $companyId);
        $query->where('contractStartDate', '<', $endMonth);
        $query->where('contractEndDate', '>', $startMonth);
        if(!is_null($type)){
            $query->where('mContractPlan.planType', $type);
        }

        $data = $query->get();

        return $data;
    }

    /**
     * 契約情報取得(請求用)
     *
     * @param $companyId
     * @param $startMonth
     * @param $endMonth
     * @return $data
     */
    public function getContractInfo($companyId, $claimMonth, $type)
    {
        $startDate = date('Y-m-d', strtotime('first day of this month' . $claimMonth));
        $endDate = date('Y-m-d', strtotime('last day of this month' . $claimMonth));
        $detailList = $this->getDetailByMonth($companyId, $startDate, $endDate, $type);

        $retAry = [];
        foreach($detailList as $detail){
            $retAry[] = [
                'companyId' => $detail->companyId,
                'contractPlanId' => $detail->contractPlanId,
                'seqNo' => $detail->seqNo,
                'contractTypeId' => $detail->contractTypeId,
                'contractStartDate' => $detail->contractStartDate,
                'contractEndDate' => $detail->contractEndDate,
                'idUnitPrice' => $detail->idUnitPrice,
                'searchUnitPrice' => $detail->searchUnitPrice,
                'searchCount' => $detail->searchCount,
            ];
        }

        return $retAry;
    }

}
