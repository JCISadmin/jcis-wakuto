<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 旧字体変換マスタ詳細
 */
class MConvertFontDetail extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mConvertFontDetail';

    /**
     * insert
     *
     * @param $targetCharacter
     * @param $convertCharacter
     * @return 
     */
    public function insertConvertFontDetail($targetCharacter,$convertCharacter){
        $items = explode("\r\n", $convertCharacter);
        foreach($items as $item){
            DB::table($this->table)->insert([
                'targetCharacter' => $targetCharacter,
                'convertCharacter' => $item
            ]);
        }

    }

    /**
     * delete
     *
     * @param $targetCharacter
     * @return 
     */
    public function deleteConvertFontDetail($targetCharacter){
        $query = DB::table($this->table);
        $query->where("targetCharacter",$targetCharacter);
        $query->delete();
    }

    /**
     * update
     *
     * @param $targetCharacter
     * @param $convertCharacter
     * @return 
     */
    public function updateConvertFontDetail($targetCharacter,$convertCharacter){
        $this->begin();

        $this->deleteConvertFontDetail($targetCharacter);
        $this->insertConvertFontDetail($targetCharacter,$convertCharacter);

        $this->commit();
    }

}
