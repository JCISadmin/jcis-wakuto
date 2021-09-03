<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

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
        $query->where('mContractPlan.planType', 'web');

        return $query->first();

    }

    /**
     * データ取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @return array
     */
    public function getDetail($companyId,$contractPlanId): array
    {
        $query = DB::table($this->table);
        $query->select(
            'userId',
            'password',
            'name',
            'departmentJob',
            'mail',
            'delFlg',
        );
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $data = $query->get();

        $ary = [];
        foreach($data as $key => $value){
            $ary[$key]['userId'] = $value->userId;
            $ary[$key]['password'] = $value->password;
            $ary[$key]['name'] = $value->name;
            $ary[$key]['departmentJob'] = $value->departmentJob;
            $ary[$key]['mail'] = $value->mail;
            $ary[$key]['delFlg'] = $value->delFlg;
        }

        return $ary;
    }
    

    /**
     * データ更新
     *
     * @param $data
     * @param $type
     */
    public function updateUserDetail($data, $type)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        foreach($data[$type]['userDetail'] as $item){
            $query = DB::table($this->table);
            $query->join('mContractPlan', function ($join) {
                $join->on('mUserDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
            });
            $query->where('mUserDetail.companyId', $data['userCompany']['companyId']);
            $query->where('mUserDetail.userId', $item['userId']);
            $query->where('mUserDetail.password', $item['password']);
            $query->where('mContractPlan.planType', $type);
            $query->update([
                'mUserDetail.companyId' => $data['userCompany']['companyId'],
                'mUserDetail.contractPlanId' => $data[$type]['contractPlanId'],
                'mUserDetail.userId' => $item['userId'],
                'mUserDetail.password' => $item['password'],
                'mUserDetail.name' => $item['name'],
                'mUserDetail.departmentJob' => $item['departmentJob'],
                'mUserDetail.mail' => $item['mail'],
                'mUserDetail.delFlg' => $item['delFlg'],
                'mUserDetail.updateDatetime' => $now,
            ]);
        }
    }

    /**
     * データ登録
     *
     * @param $data
     * @param $type
     */
    public function insertUserDetail($data, $type)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        //先頭の文字を大文字に変換

        $part ='';
        if($type === 'web'){
            $part = 'Web';
        }

        if($type === 'api'){
            $part = 'Api';
        }

        for ($i = 0; $i <= count($data['add'.$part.'UserId'])-1 ; $i++){
            $query = DB::table($this->table);
            $query->insert([
                'companyId' => $data['userCompany']['companyId'],
                'contractPlanId' => $data[$type]['contractPlanId'],
                'userId' => $data['add'.$part.'UserId'][$i],
                'password' => $this->makePassword(),
                'name' => $data['add'.$part.'Name'][$i],
                'departmentJob' => $data['add'.$part.'DepartmentJob'][$i],
                'mail' => $data['add'.$part.'DepartmentJobMail'][$i],
                'delFlg' => $data['add'.$part.'DelFlg'][$i],
                'createDatetime' => $now,
                'updateDatetime' => $now,
            ]);
        }
    }

    /**
     * データ削除
     *
     * @param $data
     * @param $type
     */
    public function deleteUserDetail($data, $type)
    {
        if(isset($data[$type]['userDetail'])){
            foreach($data[$type]['userDetail'] as $item){
                $query = DB::table($this->table);
                $query->join('mContractPlan', function ($join) {
                    $join->on('mUserDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
                });
                $query->where('mUserDetail.companyId', $data['userCompany']['companyId']);
                $query->where('mUserDetail.userId', $item['userId']);
                $query->where('mUserDetail.password', $item['password']);
                $query->where('mContractPlan.planType', $type);
                $query->delete();
            }
        }
    }

}
