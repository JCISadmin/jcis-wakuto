<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 契約タイプマスタ
 */
class MContractType extends BaseModel
{

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mContractType';

    /**
     * Select用リストの取得
     *
     * @return Collection
     */
    public function getSelectList(): Collection
    {

        return DB::table($this->table)->get();

    }

    /**
     * 契約形態名称を取得
     *
     * @return string|null
     */
    public function getTypeNameByTypeId($contractTypeId)
    {
        $query = DB::table($this->table);
        $query->where('contractTypeId', $contractTypeId);
        $data = $query->first();

        if(is_null($data)){
            return null;
        }
        return $data->name;
    }

}
