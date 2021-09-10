<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 都道府県マスタ
 */
class MPrefecture extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mPrefecture';

    /**
     * Select用リストの取得
     *
     * @return array[]
     */
    public function getSelectList(): array
    {

        $cityAry = [];
        $prefAry = [];

        $list = DB::table($this->table)->get();
        foreach ($list as $item) {
            $prefAry[$item->prefecture] = $item->prefecture;
            $cityAry[$item->prefecture][] = $item->city;
        }

        return [
            'prefecture' => $prefAry,
            'city' => $cityAry
        ];

    }
}
