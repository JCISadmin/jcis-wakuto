<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * ユーザーマスタ詳細
 */
class MUserDetail extends BaseModel
{

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mUserDetail';

    /**
     * ユーザー認証
     *
     * @param $userId
     * @param $password
     * @return object|null
     */
    public function getUserCredentials($userId, $password): ?object
    {

        $query = DB::table($this->table);
        $query->join('mContractPlan', function ($join){
            $join->on('mUserDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });

        $query->select(
            'mUserDetail.*',
            'mContractPlan.planType'
        );

        $query->where('mUserDetail.userId', $userId);
        $query->where('mUserDetail.password', $password);
        $query->where('mUserDetail.lockFlg', self::LOCK_FLG_OFF);
        $query->where('mUserDetail.delFlg', self::DEL_FLG_OFF);
        $query->whereNotNull('mUserDetail.logoutDatetime');
        $query->where('mContractPlan.planType', 'web');

        return $query->first();

    }

    /**
     *　データ取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @return $data
     */
    public function getDetail($companyId,$contractPlanId) {
        $query = DB::table($this->table);
        $query->select(
            'contractPlanId',
            'userId',
            'passWord',
            'name',
            'departmentJob',
            'mail',
            'delFlg'
        );
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $data = $query->get();

        foreach($data as $key => $value){
            $ary[$key]['contractPlanId'] = $value->contractPlanId;
            $ary[$key]['userId'] = $value->userId;
            $ary[$key]['passWord'] = $value->passWord;
            $ary[$key]['name'] = $value->name;
            $ary[$key]['departmentJob'] = $value->departmentJob;
            $ary[$key]['mail'] = $value->mail;
            $ary[$key]['delFlg'] = $value->delFlg;
        }
        
        return $ary;
    }
}
