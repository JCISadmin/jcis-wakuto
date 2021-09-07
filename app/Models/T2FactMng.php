<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use DateTime;

/**
 * 2要素認証管理マスタ
 */
class T2FactMng extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 't2FactMng';

    /**
     * 管理情報の追加
     *
     * @param $companyId
     * @param $contractId
     * @param $userId
     * @param $ipAddress
     * @param $userAgent
     */
    public function addClient($companyId, $contractId, $userId, $ipAddress, $userAgent)
    {

        $dt = new DateTime();
        $now = $dt->format('Y-m-d h:i:s');

        $manageId = substr(hash('md5', $companyId . $contractId . $userId . $ipAddress . $userAgent, false), 0, 20);

        $query = DB::table($this->table);
        $query->insert([
            'companyId' => $companyId,
            'contractPlanId' => $contractId,
            'userId' => $userId,
            'manageId' => $manageId,
            'ipAddress' => $ipAddress,
            'userAgent' => substr($userAgent, 0, 20),
            'createDatetime' => $now,
            'updateDatetime' => $now,
        ]);
    }

    /**
     * クライアント登録チェック
     *
     * @param $companyId
     * @param $contractId
     * @param $userId
     * @param $ipAddress
     * @return bool
     */
    public function checkClient($companyId, $contractId, $userId, $ipAddress): bool
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->where('contractPlanId', $contractId);
        $query->where('userId', $userId);
        $query->where('ipAddress', $ipAddress);

        $rec = $query->get();
        if (count($rec) == 0) {
            return false;
        }

        return true;

    }


}
