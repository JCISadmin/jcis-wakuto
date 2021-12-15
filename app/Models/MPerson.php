<?php

namespace App\Models;

use Datetime;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

/**
 * 個人情報
 */
class MPerson extends baseModel
{
    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'mPerson';

    /**
     * 個人情報一覧の取得
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
            DB::raw('personId as editId')
        );

        if ($inputName != '') {
            $query->where('inputName', $inputName);
        }

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);

    }

    /**
     *id指定レコードの取得
     *
     * @param $editId
     * @return null
     */
    public function get($editId) {

        $query = DB::table($this->table);
        $query->where('personId', $editId);

        return $query->first();

    }

    /**
     * 個人情報更新
     *
     * @param $data
     * @throws Exception
     */
    public function updateData($data) {

        $this->begin();

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $query = DB::table($this->table);
        $query->where('personId', $data['personId']);
        $query->update([
            'inputName' => $data['inputName'],
            'dispName' => $data['dispName'],
            'inputKana' => $data['inputKana'],
            'dispKana' => $data['dispKana'],
            'birthday' => $data['birthday'],
            'postCode' => $data['postCode'],
            'address' => $data['address'],
            'requireDivision' => $data['requireDivision'],
            'departmentJob' => $data['departmentJob'],
            'department' => $data['department'],
            'departmentAddress' => $data['departmentAddress'],
            'caseDate' => $data['caseDate'],
            'caseSummary' => $data['caseSummary'],
            'caseAge' => $data['caseAge'],
            'disposalOffice' => $data['disposalOffice'],
            'infoKind' => $data['infoKind'],
            'infoSource' => $data['infoSource'],
            'filename' => $data['filename'],
            'regDate' => $data['regDate'],
            'note' => $data['note'],
            'updateDatetime' => $now
        ]);

        $this->commit();
    }

    /**
     * 個人情報新規追加
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
     * 個人情報削除
     *
     * @param $editId
     * @throws Exception
     */
    public function deleteData($editId) {

        $this->begin();

        $query = DB::table($this->table);
        $query->where('personId', $editId);
        $query->delete();

        $this->commit();

    }


}
