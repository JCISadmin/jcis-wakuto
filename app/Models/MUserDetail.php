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
     * ユーザー認証 apiユーザー
     *
     * @param $userId
     * @param $password
     * @return object|null
     */
    public function getUserCredentialsApi($userId, $password): ?object
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
        $query->where('mContractPlan.planType', 'api');

        return $query->first();

    }

    /**
     * データ取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @return array
     */
    public function getDetail($companyId, $contractPlanId): array
    {
        $query = DB::table($this->table);
        $query->select(
            'companyId',
            'contractPlanId',
            'userId',
            'password',
            'name',
            'departmentJob',
            'mail',
            'idMailBcc',
            'delFlg',
            'delMonth',
        );
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $data = $query->get();

        $ary = [];
        foreach($data as $key => $value){
            $ary[$key]['companyId'] = $value->companyId;
            $ary[$key]['contractPlanId'] = $value->contractPlanId;
            $ary[$key]['userId'] = $value->userId;
            $ary[$key]['password'] = $value->password;
            $ary[$key]['name'] = $value->name;
            $ary[$key]['departmentJob'] = $value->departmentJob;
            $ary[$key]['mail'] = $value->mail;
            $ary[$key]['idMailBcc'] = $value->idMailBcc;
            $ary[$key]['delFlg'] = $value->delFlg;
            $ary[$key]['delMonth'] = $value->delMonth;
        }

        return $ary;
    }

    /**
     * ユーザー情報取得
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @return array
     */
    public function get($companyId, $contractPlanId, $userId): array
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);

        return (array) $query->first();
    }

    /**
     * ユーザー情報一覧取得
     *
     * @param $companyId
     * @param $type
     * @return 
     */
    public function getList($companyId, $type)
    {
        $query = DB::table($this->table);
        $query->select(
            'mUserDetail.userId',
            'mUserDetail.name',
        );
        $query->join('mContractPlan', function ($join) {
            $join->on('mUserDetail.contractPlanId', '=', 'mContractPlan.contractPlanId');
        });
        $query->where('companyId', $companyId);
        $query->where('mContractPlan.planType', $type);

        return $query->get();
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

            $list = $query->get('mUserDetail.delFlg');
            
            $delMonth = null;
            //IDが有効→無効に更新する場合、無効月を設定
            if($list[0]->delFlg == 0 && $item['delFlg'] == 1){
                $delMonth = $dt->format('Ym');
            }

            $query->update([
                'mUserDetail.companyId' => $data['userCompany']['companyId'],
                'mUserDetail.contractPlanId' => $data[$type]['contractPlanId'],
                'mUserDetail.userId' => $item['userId'],
                'mUserDetail.password' => $item['password'],
                'mUserDetail.name' => $item['name'],
                'mUserDetail.departmentJob' => $item['departmentJob'],
                'mUserDetail.mail' => $item['mail'],
                'mUserDetail.idMailBcc' => $item['idMailBcc'],
                'mUserDetail.delFlg' => $item['delFlg'],
                'mUserDetail.delMonth' => $delMonth,
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
                'idMailBcc' => $data['add'.$part.'DepartmentJobidMailBcc'][$i],
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

    /**
     * パスワード変更
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @return string
     */
    public function changePassword($companyId, $contractPlanId, $userId): string
    {

        $password = $this->makePassword();

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);
        $query->update(['password'=> $password]);

        return $password;

    }

    /**
     * LoginDatetimeの更新
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $loginTime
     */
    public function updateLoginTime($companyId, $contractPlanId, $userId, $loginTime) {

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractPlanId);
        $query->where('userId', $userId);
        $query->update(['loginDatetime'=> $loginTime]);

    }

    /**
     * ユーザ一覧を取得(会社ID指定)
     *
     * @param $companyId
     * @return
     */
    public function getUserListByCompanyId($companyId) {

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);

        return $query->get();
    }

    /**
     * ユーザ担当者名を取得
     *
     * @param $companyId
     * @param $userId
     * @return string
     */
    public function getUserName($companyId, $userId) {

        $query = DB::table($this->table);
        $query->select(
            'mUserDetail.name',
        );

        $query->where('companyId', $companyId);
        $query->where('userId', $userId);
        $data = $query->first();

        if(is_null($data)){
            return null;
        }

        return (string) $data->name;
    }
}
