<?php

namespace App\Models;

use Datetime;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 法人情報
 */
class MCorporation extends BaseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mCorporation';

    /**
     * 法人情報一覧の取得
     *
     * @param $inputName
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($inputName, $pageLine): LengthAwarePaginator
    {

        $query = DB::table($this->table);
        $query->select(
            '*',
            DB::raw('corporationId as editId')
        );

        if ($inputName != '') {
            $query->where('inputName', 'like', '%' . $inputName . '%');
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }

    /**
     * id指定レコードの取得
     *
     * @param $editId
     * @return object|null
     */
    public function get($editId): object|null
    {

        $query = DB::table($this->table);
        $query->where('corporationId', $editId);

        return $query->first();

    }

    /**
     * 法人情報更新
     *
     * @param $data
     * @throws Exception
     */
    public function updateData($data){

        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->where('corporationId', $data['corporationId']);
        $query->update([
            'inputName' => $data['inputName'],
            'dispName' => $data['dispName'],
            //inputNameを半角変換して保存
            'uniCaseName' => mb_convert_kana($data['inputName'], "rnska"),
            'industry' => $data['industry'],
            'postCode' => $data['postCode'],
            'address' => $data['address'],
            'corporateCode' => $data['corporateCode'],
            'tel' => $data['tel'],
            'requireDivision' => $data['requireDivision'],
            'businessOwner' => $data['businessOwner'],
            'department' => $data['department'],
            'delegate' => $data['delegate'],
            'casePersonName' => $data['casePersonName'],
            'caseDate' => $data['caseDate'],
            'caseSummary' => str_replace(array("\r", "\n"), '', $data['caseSummary']),
            'disposalOffice' => $data['disposalOffice'],
            'infoKind' => $data['infoKind'],
            'infoSource' => $data['infoSource'],
            'filename' => $data['filename'],
            'regDate' => $data['regDate'],
            'note' => str_replace(array("\r", "\n"), '', $data['note']),
            'updateDatetime' => $now
        ]);

        $this->commit();
    }

    /**
     * 法人情報新規追加
     *
     * @param $data
     * @throws Exception
     */
    public function ins($data) {

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $data['createDatetime'] = $now;
        $data['updateDatetime'] = $now;

        DB::table($this->table)->insert($data);

    }



    /**
     * 法人情報削除
     *
     * @param $editId
     * @throws Exception
     */
    public function deleteData($editId) {

        $this->begin();

        $query = DB::table($this->table);
        $query->where('corporationId', $editId);
        $query->delete();

        $this->commit();

    }


}
