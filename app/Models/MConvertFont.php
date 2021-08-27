<?php

namespace App\Models;

use Datetime;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 旧字体変換マスタ
 */
class MConvertFont extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mConvertFont';

    /**
     * 旧字体変換一覧の取得
     *
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($pageLine): LengthAwarePaginator
    {

        $query = DB::table($this->table);
        $query->select('targetCharacter',
                DB::Raw('
                (
                    select group_concat(convertCharacter)
                    from mConvertFontDetail
                    where mConvertFontDetail.targetCharacter = mConvertFont.targetCharacter
                ) as convertCharacter
                ')
        );

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }   

    /**
     * insert
     *
     * @param $targetCharacter
     * @param $convertCharacter
     * @return 
     */
    public function insertConvertFont($targetCharacter,$convertCharacter){
        $this->begin();

        DB::table($this->table)->insert([
            'targetCharacter' => $targetCharacter,
        ]);

        //MConvertFontDetailを削除
        $model = new MConvertFontDetail();
        $model->insertConvertFontDetail($targetCharacter,$convertCharacter);

        $this->commit();
    }

    /**
     * delete
     *
     * @param $targetCharacter
     * @return 
     */
    public function deleteConvertFont($targetCharacter)
    {
        $this->begin();

        $query = DB::table($this->table);
        $query->where("targetCharacter",$targetCharacter);
        $query->delete();

        //MConvertFontDetailを削除
        $model = new MConvertFontDetail();
        $model->deleteConvertFontDetail($targetCharacter);

        $this->commit();
    }


}
