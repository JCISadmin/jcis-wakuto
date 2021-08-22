<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * ユーザーマスタ
 */
class MUserCompany extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mUserCompany';

    /*
        TODO ユーザー一覧用SQLサンプル
　　　　　　select uc.*
                ,webPlan.name as webPlanName
                ,apiPlan.name as apiPlanName
            from mUserCompany as uc
            left join
                (
                    select ct.*, cp.name
                      from tContractPlan as ct
                      left join mContractPlan as cp
                             on ct.contractPlanId = cp.contractPlanId
                      where cp.planType = 'web'
                ) as webPlan
                on uc.companyId = webPlan.companyId and uc.companyId = webPlan.companyId
            left join
                 (
                    select ct.*, cp.name
                      from tContractPlan as ct
                      left join mContractPlan as cp
                                     on ct.contractPlanId = cp.contractPlanId
                     where cp.planType = 'api'
                 ) as apiPlan
                 on uc.companyId = apiPlan.companyId and uc.companyId = apiPlan.companyId

     */

}
