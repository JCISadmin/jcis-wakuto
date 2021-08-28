<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

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
     * データ取得
     *
     * @param $editId
     * @return Collection
     */
    public function get($editId): Collection
    {
        $query = DB::table($this->table);
        $query->where('targetCharacter', $editId);

        return $query->get();
    }

    /**
     * 更新処理
     *
     * @param $data
     * @throws Exception
     */
    public function updateFont($data)
    {
        $this->begin();

        $query = DB::table($this->table);
        $query->where('targetCharacter', $data['targetCharacter']);
        $query->delete();

        $this->insertFont($data);

        $this->commit();
    }

    /**
     * 登録
     *
     * @param $data
     */
    public function insertFont($data)
    {

        foreach ($data['convertCharacterAry'] as $item) {
            $query = DB::table($this->table);
            $query->insert([
                'targetCharacter' => $data['targetCharacter'],
                'convertCharacter' => $item
            ]);
        }

    }

    /**
     * 削除
     *
     * @param $id
     */
    public function deleteFont($id)
    {

        $query = DB::table($this->table);
        $query->where('targetCharacter', $id);
        $query->delete();

    }


}
