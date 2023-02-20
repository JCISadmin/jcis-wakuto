<?php

namespace App\Models;

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
     * @param $targetCharacter
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($targetCharacter, $pageLine): LengthAwarePaginator
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

        if ($targetCharacter != '') {
            $query->where('targetCharacter', $targetCharacter);
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }

    /**
     * 取得
     *
     * @param $id
     * @return object|null
     */
    public function get($id): ?object
    {
        $query = DB::table($this->table);
        $query->where('targetCharacter', $id);

        return $query->first();
    }

    /**
     * 登録
     *
     * @param $data
     * @throws Exception
     */
    public function insertFont($data)
    {
        $this->begin();

        DB::table($this->table)->insert([
            'targetCharacter' => $data['targetCharacter'],
        ]);

        $model = new MConvertFontDetail();
        $model->insertFont($data);

        $this->commit();
    }

    /**
     * 削除
     *
     * @param $id
     * @throws Exception
     */
    public function deleteFont($id)
    {
        $this->begin();

        $query = DB::table($this->table);
        $query->where('targetCharacter', $id);
        $query->delete();

        $model = new MConvertFontDetail();
        $model->deleteFont($id);

        $this->commit();
    }

}
