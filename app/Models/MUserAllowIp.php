<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;

/**
 *  接続許可IPアドレス
 */
class MUserAllowIp extends BaseModel
{

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mUserAllowIp';

    /**
     * 情報取得
     *
     * @param $companyId
     * @return array
     */
    public function get($companyId): array
    {
        $query = DB::table($this->table);
        $query->where('companyId', $companyId);

        $list = $query->get();

        return $list->all();
    }

    /**
     * 登録
     *
     * @param $data
     * @throws Exception
     */
    public function ins($companyId,$ipAddressList)
    {
        $dt = new Datetime();
        $now = $dt->format('Y-m-d h:i:s');

        DB::table($this->table)->where('companyId', $companyId)->delete();

        $query = DB::table($this->table);

        $seqNo = 1;
        foreach($ipAddressList as $ipAddress){

            $query->insert([
                'companyId' => $companyId,
                'seqNo' => $seqNo,
                'ipAddress' => $ipAddress,
                'createDatetime' => $now,
                'updateDatetime' => $now
            ]);

            $seqNo++;
        }
    }

}
