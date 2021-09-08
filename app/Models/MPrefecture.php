<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
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
     * @return Collection
     */
    public function getSelectList(): Collection
    {

        return DB::table($this->table)->get();

    }
}
