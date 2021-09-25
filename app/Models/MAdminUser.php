<?php

namespace App\Models;

use Datetime;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 管理者ユーザー
 */
class MAdminUser extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mAdminUser';

    /**
     * 管理ユーザー認証
     *
     * @param $userId
     * @param $password
     * @return object|null
     */
    public function getUserCredentials($userId, $password): ?object
    {

        $query = DB::table($this->table);
        $query->select(
            '*',
            DB::raw("'admin' as companyId"),
            DB::raw("0 as contractPlanId")
        );
        $query->where('userId', $userId);
        $query->where('password', $password);
        $query->where('lockFlg', self::LOCK_FLG_OFF);
        $query->where('delFlg', self::DEL_FLG_OFF);

        return $query->first();

    }

    /**
     * 管理ユーザー一覧の取得
     *
     * @param $userId
     * @param $userName
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($userId, $userName, $pageLine): LengthAwarePaginator
    {

        $query = DB::table($this->table);

        if ($userId != '') {
            $query->where('userId', 'like', '%' . $userId . '%');
        }

        if ($userName != '') {
            $query->where('userName', 'like', '%' . $userName . '%');
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }

    /**
     * 管理ユーザ更新
     *
     * @param $data
     * @throws Exception
     */
    public function updateUser($data) {

        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        // 既存ユーザーの更新
        foreach ($data['userInfo'] as $user) {

            // 重複チェック
            $cnt = DB::table($this->table)->where('userId', $user['userId'])->count();
            if ($user['userId'] == $user['userIdOrg']) {
                $chkCnt = 1;
            } else {
                $chkCnt = 0;
            }

            if ($cnt > $chkCnt) {
                $this->rollback();
                throw new Exception('duplicate');
            }

            $query = DB::table($this->table);
            $query->where('userId', $user['userIdOrg']);
            $query->update([
                'userId' => $user['userId'],
                'userName' => $user['userName'],
                'mail' => $user['mail'],
                'delFlg' => $user['delFlg'],
                'createDatetime' => $user['createDatetime'],
                'updateDatetime' => $now
            ]);

        }

        // 新規ユーザーの登録
        if (isset($data['addUserId'])) {
            foreach ($data['addUserId'] as $key => $userId) {
                // 重複チェック
                $cnt = DB::table($this->table)->where('userId', $userId)->count();
                if ($cnt > 0) {
                    $this->rollback();
                    throw new Exception('duplicate');
                }

                DB::table($this->table)->insert([
                    'userId' => $userId,
                    'password' => $this->makePassword(),
                    'userName' => $data['addUserName'][$key],
                    'mail' => $data['addMail'][$key],
                    'delFlg' => self::DEL_FLG_OFF,
                    'lockFlg' => self::LOCK_FLG_OFF,
                    'createDatetime' => $now,
                    'updateDatetime' => $now
                ]);

            }
        }

        $this->commit();

    }

}
