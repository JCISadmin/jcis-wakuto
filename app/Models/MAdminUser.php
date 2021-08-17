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

}
