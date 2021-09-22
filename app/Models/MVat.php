<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 個人情報
 */
class MVat extends baseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mVat';

    /**
     * 請求時点の税率を取得
     *
     * @param $claimDate
     * @return mixed
     */
    public function getTax($claimDate): mixed
    {
        $tax = 1;

        $query = DB::table($this->table);
        $query->select(
            'tax',
        );
        $query->where('startDate', '<=', $claimDate);
        $query->where('endDate', '>=', $claimDate);
        $data = $query->first();

        if(is_null($data) === false){
            $tax = $data->tax;
        }

        return $tax;
    }

}
