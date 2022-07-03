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

    const DATE_LOW_VALUE = '2000-01-01';
    const DATE_HIGH_VALUE = '3000-01-01';

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';

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
        $webPlan->where('mContractPlan.planType', 'web');

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
        $apiPlan->where('mContractPlan.planType', 'api');
        
        $query = DB::table('tContractPlanDetail');

        $query->select(
            'tContractPlanDetail.companyId',
            'tContractPlanDetail.seqNo',
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

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'webPlan.companyId');
            $join->on('tContractPlanDetail.seqNo', '=', 'webPlan.seqNo');
        });

        $query->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('tContractPlanDetail.companyId', '=', 'apiPlan.companyId');
            $join->on('tContractPlanDetail.seqNo', '=', 'apiPlan.seqNo');
        });

        $query->where('tContractPlanDetail.companyId', $companyId);
        $query->groupBy(['tContractPlanDetail.companyId','tContractPlanDetail.seqNo']);


        return $query->get();
    }

    /**
     * 一覧を取得
     *
     * @param $companyId
     * @return $list
     */
    public function getDetail($companyId, $type, $seqNo)
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

        //seqNo最大(最新の変更)データを取得
        if($seqNo === ''){
            $seqNo = $query->max('seqNo');
        }

        $query->where('tContractPlanDetail.seqNo', $seqNo);

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

        $contractStartDate = $this->getContractStartDate($data[$type]['startTrial'], $data[$type]['useStartDate']);

        $query = DB::table($this->table);
        
        $query->insert([
            'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
            'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
            //新規追加はseqNo=1
            'tContractPlanDetail.seqNo' => 1,
            'tContractPlanDetail.contractStartDate' => $contractStartDate,
            'tContractPlanDetail.contractEndDate' => self::DATE_HIGH_VALUE,
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

        $count = $this->getDataCountByTypeId($data['userCompany']['companyId'], $type);

        //既存データあり
        if($count > 0){
            //対象seqNoレコードを更新
            $updQuery = DB::table($this->table);
            $updQuery->join('mContractPlan', function ($join) {
                $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $updQuery->where('companyId', $data['userCompany']['companyId']);
            $updQuery->where('mContractPlan.planType', $type);
            $updQuery->where('seqNo', $seqNo);
            
            $updQuery->update([
                'tContractPlanDetail.companyId' => $data['userCompany']['companyId'],
                'tContractPlanDetail.contractPlanId' => $data[$type]['contractPlanId'],
                'tContractPlanDetail.seqNo' => $seqNo,
                'tContractPlanDetail.contractEndDate' => self::DATE_HIGH_VALUE,
                'tContractPlanDetail.contractTypeId' => $data[$type]['contractTypeId'],
                'tContractPlanDetail.idUnitPrice' => $data[$type]['idUnitPrice'],
                'tContractPlanDetail.searchUnitPrice' => $data[$type]['searchUnitPrice'],
                'tContractPlanDetail.searchCount' => $data[$type]['searchCount'],
                'tContractPlanDetail.updateDatetime' => $now,
            ]);
    
            //contractStartDateをトライアル開始日or利用開始日で更新
            $contractStartDate = $this->getContractStartDate($data[$type]['startTrial'], $data[$type]['useStartDate']);
            
            $updQuery->update([
                'tContractPlanDetail.contractStartDate' => $contractStartDate,
            ]);

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
            'tContractPlanDetail.contractEndDate' => self::DATE_HIGH_VALUE,
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
     * 契約開始日を取得(トライアル開始日と利用開始日を比較)
     *
     * @param $data
     * @param $type
     * @return $contractStartDate
     */
    public function getContractStartDate($startTrial, $useStartDate) {
   
        //トライアル・利用開始日が未入力の場合、contractStartDateをnullで更新
        if(is_null($startTrial) && is_null($useStartDate)){
                $contractStartDate = null;
        }else{
            //未入力の場合日付を最小値に設定
            if(is_null($startTrial)){
            $startTrial = self::DATE_HIGH_VALUE;
            }
            if(is_null($useStartDate)){
                $useStartDate = self::DATE_HIGH_VALUE;
            }
            //トライアル・利用開始日で日付が早い方をcontractStartDateとして保存
            if($startTrial < $useStartDate){
                $contractStartDate = $startTrial;
            }else{
                $contractStartDate = $useStartDate;
            }
        }
    
        return $contractStartDate;
    }

    /**
     * 会社ID指定で最大seqNoを取得
     *
     * @param $data
     * @param $type
     * @return $contractStartDate
     */
    public function getMaxSeqNo($companyId) {
    
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        
        $seqNo = $query->max('seqNo');

        if($seqNo === null){
            $seqNo = '';
        }

        return $seqNo;
    }

    /**
     * 既存データが存在するかチェック
     *
     * @param $data
     * @param $type
     * @return $count
     */
    public function getDataCountByTypeId($companyId, $type) {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as count'));

        $query->join('mContractPlan', function ($join) {
            $join->on('tContractPlanDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->where('tContractPlanDetail.companyId', $companyId);
        $query->where('mContractPlan.planType', $type);
        $count = $query->first();

        return $count->count;
    }


    /**
     * 既存データが存在するかチェック
     *
     * @param $data
     * @param $type
     * @return $count
     */
    public function getTargetSeqNo($contractStartDate, $contractEndDate) {


    }
}
