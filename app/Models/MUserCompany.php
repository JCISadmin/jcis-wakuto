<?php

namespace App\Models;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';


    /**
     * ユーザー一覧の取得
     *
     * @param $companyName
     * @param $contractStatus
     * @param $contractPlan
     * @param $useEndAlertDate
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($companyName, $contractStatus, $contractPlan, $useEndAlertDate, $pageLine): LengthAwarePaginator
    {

        $idNum = DB::table('mUserDetail');
        $idNum->select(
            'companyId',
            'contractPlanId',
            DB::raw('count(*) as ids')
        );
        $idNum->where('delFlg', self::DEL_FLG_OFF);
        $idNum->groupBy(['companyId', 'contractPlanId']);

        $webPlan = DB::table('tContractPlan');
        $webPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'webPlanIds.ids'
        );
        $webPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $webPlan->joinSub($idNum, 'webPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'webPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'webPlanIds.contractPlanId');
        });
        $webPlan->where('mContractPlan.planType', 'web');

        $apiPlan = DB::table('tContractPlan');
        $apiPlan->select(
            'tContractPlan.*',
            'mContractPlan.name',
            'mContractPlan.planType',
            'apiPlanIds.ids'
        );
        $apiPlan->join('mContractPlan', function ($join) {
            $join->on('tContractPlan.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $apiPlan->joinSub($idNum, 'apiPlanIds', function($join){
            $join->on('tContractPlan.companyId', '=', 'apiPlanIds.companyId');
            $join->on('tContractPlan.contractPlanId', '=', 'apiPlanIds.contractPlanId');
        });
        $apiPlan->where('mContractPlan.planType', 'api');

        $user = DB::table('mUserCompany');

        $user->select(
            'mUserCompany.*',
            'webPlan.contractPlanId as webPlanPlanId',
            'webPlan.name as webPlanName',
            'webPlan.useEndAlertDate as webPlanUseEndAlertDate',
            'webPlan.useEndDate as webPlanUseEndDate',
            'webPlan.ids as webPlanIds',
            'apiPlan.contractPlanId as apiPlanPlanId',
            'apiPlan.name as apiPlanName',
            'apiPlan.useEndAlertDate as apiPlanUseEndAlertDate',
            'apiPlan.useEndDate as apiPlanUseEndDate',
            'apiPlan.ids as apiPlanIds',
            'mContractStatus.name as statusName',
        );

        $user->leftJoinSub($webPlan, 'webPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'webPlan.companyId');
        });

        $user->leftJoinSub($apiPlan, 'apiPlan', function($join){
            $join->on('mUserCompany.companyId', '=', 'apiPlan.companyId');
        });

        $user->leftJoin('mContractStatus', function($join){
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });


        /* @var string $user */
        $query = DB::table($user);
        $query->where('delFlg', self::DEL_FLG_OFF);

        if ($companyName != '') {
            $query->where('name', 'like', '%' . $companyName . '%');
        }

        if ($contractStatus != '') {
            $query->where('contractStatus', $contractStatus);
        }

        if ($contractPlan != '') {
            $query->whereRaw('(webPlanPlanId = ? or apiPlanPlanId = ?)', [$contractPlan, $contractPlan]);
        }

        if ($useEndAlertDate != '') {
            $query->where('useEndAlertDate', $useEndAlertDate);
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }


    /**
     * ユーザー詳細の取得
     *
     * @param $companyId
     * @return array
     */
    public function get($companyId): array
    {
        $model = new TContractPlan();
        $data = [];

        $query = DB::table($this->table);
        $query->select(
            'mContractStatus.contractStatus',
            'mContractStatus.name as contractStatusName',
            'mUserCompany.chargeName',
            'mUserCompany.chargeMail',
            'mUserCompany.name',
            'mUserCompany.companyId',
            'mUserCompany.postCode',
            'mUserCompany.address',
            'mUserCompany.tel',
            'mUserCompany.staffName',
            'mUserCompany.staffDepartmentJob',
            'mUserCompany.staffTel',
            'mUserCompany.staffMail',
            'mUserCompany.claimName',
            'mUserCompany.claimDepartmentJob',
            'mUserCompany.claimTel',
            'mUserCompany.claimMailTo',
            'mUserCompany.claimMailCc',
		);

        $query->join('mContractStatus', function ($join) {
            $join->on('mUserCompany.contractStatus', '=', 'mContractStatus.contractStatus');
        });

        $query->where('mUserCompany.companyId', $companyId);
        $userCompany =  (array)$query->first();

        $data['userCompany'] = $userCompany;

        $data['contractPlan']['web'] = $model->getPlan($companyId, self::TYPE_WEB);
        $data['contractPlan']['api'] = $model->getPlan($companyId, self::TYPE_WEB);

        return($data);
    }
}
