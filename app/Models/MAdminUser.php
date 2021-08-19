<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public function getUserCredentials($userId, $password) {

        $query = DB::table($this->table);
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList($userId, $userName, $pageLine = '') {

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


}
