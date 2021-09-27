<?php

namespace App\Models;

use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

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
            $query->whereRaw('(webPlanUseEndAlertDate = ? or apiPlanUseEndAlertDate = ?)', [$useEndAlertDate, $useEndAlertDate]);
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
        $data['contractPlan']['api'] = $model->getPlan($companyId, self::TYPE_API);

        return($data);
    }

    /**
     * ユーザー詳細の更新
     *
     * @param $data
     * @throws Exception
     */
    public function upd($data){

        $contractPlanModel = new TContractPlan();
        $userDetailModel = new MUserDetail();

        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->where('companyId', $data['userCompany']['companyId']);
        $query->update([
            'companyId' => $data['userCompany']['companyId'],
            'name' => $data['userCompany']['name'],
            'postCode' => $data['userCompany']['postCode'],
            'address' => $data['userCompany']['address'],
            'tel' => $data['userCompany']['tel'],
            'staffName' => $data['userCompany']['staffName'],
            'staffDepartmentJob' => $data['userCompany']['staffDepartmentJob'],
            'staffTel' => $data['userCompany']['staffTel'],
            'staffMail' => $data['userCompany']['staffMail'],
            'claimName' => $data['userCompany']['claimName'],
            'claimDepartmentJob' => $data['userCompany']['claimDepartmentJob'],
            'claimTel' => $data['userCompany']['claimTel'],
            'claimMailTo' => $data['userCompany']['claimMailTo'],
            'claimMailCc' => $data['userCompany']['claimMailCc'],
            'contractStatus' => $data['userCompany']['contractStatus'],
            'chargeName' => $data['userCompany']['chargeName'],
            'chargeMail' => $data['userCompany']['chargeMail'],
            'updateDatetime' => $now
        ]);


        if(is_null($data['web']['contractPlanId']) === false){
            //WEB契約あり
            $contractPlanModel->updatePlan($data, self::TYPE_WEB);

            if(array_key_exists('userDetail', $data[self::TYPE_WEB])){
                //ユーザー情報有り
                $userDetailModel->updateUserDetail($data, self::TYPE_WEB);
            }

            //ユーザー情報の追加行の有無を検索
            $addWebFlg = false;
            if (isset($data['addWebUserId'])) {
                $addWebFlg = true;
            }

            if($addWebFlg){
                //ユーザー情報の追加有り
                $userDetailModel->insertUserDetail($data, self::TYPE_WEB);
            }
        }else{
            $contractPlanModel->deletePlan($data, self::TYPE_WEB);
            $userDetailModel->deleteUserDetail($data, self::TYPE_WEB);
        }

        if(is_null($data['api']['contractPlanId']) === false){
            //API契約あり
            $contractPlanModel->UpdatePlan($data, self::TYPE_API);

            if(array_key_exists('userDetail', $data[self::TYPE_API])){
                //ユーザー情報有り
                $userDetailModel->updateUserDetail($data, self::TYPE_API);
            }
            //ユーザー情報の追加行の有無を検索
            $addApiFlg = false;
            if (isset($data['addApiUserId'])) {
                $addApiFlg = true;
            }

            if($addApiFlg){
                //ユーザー情報の追加有り
                $userDetailModel->insertUserDetail($data, self::TYPE_API);
            }
        }else{
            $contractPlanModel->deletePlan($data, self::TYPE_API);
            $userDetailModel->deleteUserDetail($data, self::TYPE_API);
        }

        $this->commit();
    }

    /**
     * ユーザー詳細の追加
     *
     * @param $data
     * @throws Exception
     */
    public function ins($data){    
        $contractPlanModel = new TContractPlan();
        $userDetailModel = new MUserDetail();

        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        DB::table($this->table)->insert([
            'companyId' => $data['userCompany']['companyId'],
            'name' => $data['userCompany']['name'],
            'postCode' => $data['userCompany']['postCode'],
            'address' => $data['userCompany']['address'],
            'tel' => $data['userCompany']['tel'],
            'staffName' => $data['userCompany']['staffName'],
            'staffDepartmentJob' => $data['userCompany']['staffDepartmentJob'],
            'staffTel' => $data['userCompany']['staffTel'],
            'staffMail' => $data['userCompany']['staffMail'],
            'claimName' => $data['userCompany']['claimName'],
            'claimDepartmentJob' => $data['userCompany']['claimDepartmentJob'],
            'claimTel' => $data['userCompany']['claimTel'],
            'claimMailTo' => $data['userCompany']['claimMailTo'],
            'claimMailCc' => $data['userCompany']['claimMailCc'],
            'contractStatus' => $data['userCompany']['contractStatus'],
            'chargeName' => $data['userCompany']['chargeName'],
            'chargeMail' => $data['userCompany']['chargeMail'],
            'createDatetime' => $now,
            'updateDatetime' => $now,
        ]);

        if(is_null($data['web']['contractPlanId']) === false){
            //WEB契約あり
            $contractPlanModel->insertPlan($data, self::TYPE_WEB);

            //ユーザー情報の追加行の有無を検索
            $addWebFlg = false;
            if (isset($data['addWebUserId'])) {
                $addWebFlg = true;
            }

            if($addWebFlg){
                //ユーザー情報の追加有り
                $userDetailModel->insertUserDetail($data, self::TYPE_WEB);
            }
        }

        if(is_null($data['api']['contractPlanId']) === false){
            //API契約あり
            $contractPlanModel->insertPlan($data, self::TYPE_API);

            //ユーザー情報の追加行の有無を検索
            $addApiFlg = false;
            if (isset($data['addApiUserId'])) {
                $addApiFlg = true;
            }

            if($addApiFlg){
                //ユーザー情報の追加有り
                $userDetailModel->insertUserDetail($data, self::TYPE_API);
            }
        }

        $this->commit();
    }

}
