<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use App\Models\TKeywordPreviousHistory;
use App\Models\TKeywordHistoryDetail;

/**
 * 検索
 */
class TKeywordHistory extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tKeywordHistory';

    /**
     * 検索キーワード履歴登録
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $keywordHash
     * @throws Exception
     */
    public function ins($companyId, $contractPlanId, $userId, $keywordHash)
    {

        if ($companyId == 'admin') {
            return;
        }

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');
        $model = new TContractPlan();
        $detailModel = new TContractPlanDetail();

        // １年以内に検索されているか
        $isSearchedYear = $this->checkSearchedYear($companyId, $contractPlanId, $userId, $keywordHash, $now, true);
        if ($isSearchedYear) {
            return;
        }

        //課金フラグを設定
        $plan = $model->getPlanUsePlanId($companyId, $contractPlanId);
        $detailPlan = $detailModel->getPlanUsePlanId($companyId, $contractPlanId);

        $chargeFlg = self::CHARGE_FLG_OFF;
        if(is_null($plan->useStartDate) === false){
            if($detailPlan->contractTypeId === self::DEPOSIT_USE_PLAN_TYPE){
                //全額デポジットの場合
                if($now >= $plan->useStartDate && $plan->deposit == 0){
                    //本契約中、かつ検索時のデポジット残高が0の場合に、課金フラグをON
                    $chargeFlg = $this::CHARGE_FLG_ON;
                }
            }
        }

        $this->begin();

        try {

            $query = DB::table($this->table);
            $query->insert([
                'companyId' => $companyId,
                'contractPlanId' => $contractPlanId,
                'userId' => $userId,
                'hash' => $keywordHash,
                'keyword' => $keywordHash,
                'searchDate' => $now,
                'chargeFlg' => $chargeFlg,
            ]);

            // デポジット減算
            $model->useDeposit($companyId, $contractPlanId);

            $this->commit();

        } catch (QueryException $e) {
            $this->rollback();
            // Duplicate error　の際、tKeywordHistoryDetailにインサートorアップデート。
            if ($e->getCode() != '23000') {
                throw $e;
            } else {
                $keywordDetailModel = new TKeywordHistoryDetail();
                $keywordDetailModel->ins($companyId, $userId, $now);
            }
        }

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
            $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $query->where('mContractPlan.planType', $type); 

        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();
        $countSearch = $count->countSearch;

        $keywordPreviousModel = new TKeywordPreviousHistory();
        $countSearch += $keywordPreviousModel->getSearchCount($companyId, $type, $userId, $startDate, $endDate);

        return $countSearch;
    }

    /**
     * 指定期間の課金検索数を取得
     *
     * @param $companyId
     * @param $type
     * @param $userId
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getChargeSearchCount($companyId, $type, $userId, $startDate, $endDate): mixed
    {

        $query = DB::table($this->table);
        $query->select(DB::raw('count(*) as countChargeSearch'));
        $query->where('companyId', $companyId);
        $query->join('mContractPlan', function ($join) {
            $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        if(is_null($userId) === false){
            $query->where('userId', $userId);
        }
        $query->where('mContractPlan.planType', $type); 
        $query->where('chargeFlg', self::CHARGE_FLG_ON);

        $query->whereBetween('searchDate', [$startDate, $endDate]);
        $count = $query->first();
        $countSearch = $count->countChargeSearch;

        $keywordPreviousModel = new TKeywordPreviousHistory();
        $countSearch += $keywordPreviousModel->getChargeSearchCount($companyId, $type, $startDate, $endDate);

        return $count->countChargeSearch;
    }

    /**
     * 指定期間の検索数を取得(レポート機能用)
     *
     * @param $companyId
     * @param $userIds
     * @param $type
     * @param $startDate
     * @param $endDate
     * @param $trialFlg
     * @return mixed
     */
    public function getSearchCountByReport($companyId, $userIds, $type, $startDate, $endDate, $trialFlg = false): mixed
    {
        $retAry = [];

        foreach($userIds as $userId){
            $query = DB::table($this->table);
            $query->select(
                'tKeywordHistory.userId',
                'mUserDetail.name',
                'tKeywordHistory.chargeFlg',
                DB::raw('count(*) as searchCount',
            ));
            $query->leftJoin('mContractPlan', function ($join) {
                $join->on('tKeywordHistory.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $query->leftJoin('mUserDetail', function ($join) {
                $join->on('tKeywordHistory.userId', '=', 'mUserDetail.userId');
            });

            $query->where('tKeywordHistory.companyId', $companyId);
            $query->where('tKeywordHistory.userId', $userId->userId);
            $query->where('mContractPlan.planType', $type);
            $query->whereBetween('searchDate', [$startDate, $endDate]);
            $query->groupBy([
                'tKeywordHistory.userId',
                'mUserDetail.name',
                'tKeywordHistory.chargeFlg',
            ]);
        
            $list = $query->get();

            //取得データが無い場合 空データを生成
            if($list->isEmpty()){
                //トライアルの場合 データ生成なし
                if($trialFlg){
                    return null;
                }

                $retAry[] = [
                    'userId' => $userId->userId,
                    'name' => $userId->name,
                    'chargeFlg' => 0,
                    'searchCount' => 0,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                ];
            }else{

                foreach($list as $item){
                    $retAry[] = [
                        'userId' => $userId->userId,
                        'name' => $userId->name,
                        'chargeFlg' => $item->chargeFlg,
                        'searchCount' => $item->searchCount,
                        'startDate' => $startDate,
                        'endDate' => $endDate,
                    ];
                }
            }

        }

        return $retAry;
    }

    /**
     * 過去１年間で同一ワードで検索されたか
     * 
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $keywordHash
     * @param $isArchives
     * @return bool
     */
    public function checkSearchedYear($companyId, $contractPlanId, $userId, $keywordHash, $now, $isArchives = false) {

        $query = DB::table($this->table);

        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);
        $query->where('hash', $keywordHash);

        $data = $query->first();

        // 過去に検索されていない場合
        if (is_null($data)) {
            return false;
        }

        // １年以上前の場合
        $searchDate = new Datetime($data->searchDate);
        // modify引数の値はユーザーごとに設定
        $searchDate->modify('+1 year');
        $searchDateFormat = $searchDate->format('Y-m-d');
        if ($searchDateFormat < $now) {

            if ($isArchives) {
                // 検索データを過去テーブルに移動
                $keywordPreviousModel = new TKeywordPreviousHistory();
                $keywordPreviousModel->ins($data);

                // オリジナルデータ削除処理
                $this->del($companyId, $contractPlanId, $userId, $keywordHash);
            }

            return false;
        }

        // １年以内の場合
        return true;
    }

    /**
     * 削除処理
     * 
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $keywordHash
     */
    public function del($companyId, $contractPlanId, $userId, $keywordHash) {

        $query = DB::table($this->table);

        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);
        $query->where('hash', $keywordHash);

        $query->delete();
    }
}