<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Datetime;
use Illuminate\Support\Collection;

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
     * IPアドレスを取得(ログインチェック用)
     *
     * @param $companyId
     * @return Collection
     */
    public function getIpAddress($companyId): Collection
    {
        $query = DB::table($this->table);
        $query->select('ipAddress');
        $query->where('companyId', $companyId);

        $list = $query->get();

        return $list;
    }

    /**
     * 登録(delete-insert)
     *
     * @param $companyId
     * @param $ipAddressList
     * @throws Exception
     */
    public function delIns($companyId, $ipAddressList)
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
