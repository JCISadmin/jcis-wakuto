<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 契約プランマスタ
 */
class MContractPlan extends BaseModel
{

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mContractPlan';

    /**
     * Select用リストの取得
     *
     * @return Collection
     */
    public function getSelectList(): Collection
    {

        $trialPlanId = config('hds.contract.trialPlan');
        $webTrial = $trialPlanId['web'];
        $apiTrial = $trialPlanId['api'];
        
        $query = DB::table($this->table);
        $query->select('*');
        $query->where('contractPlanId', '<>', $webTrial);
        $query->where('contractPlanId', '<>', $apiTrial);
        $data = $query->get();

        return $data;

    }

    /**
     * プラン取得
     *
     * @param $contractPlanId
     * @return Object|null
     */
    public function get($contractPlanId): Object|null
    {
        return DB::table($this->table)->where('contractPlanId', $contractPlanId)->first();
    }



}
