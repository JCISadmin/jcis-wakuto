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
     * @param $inpputName
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($inputName, $pageLine): LengthAwarePaginator
    {

        $query = DB::table($this->table);

        if ($inputName != '') {
            $query->where('inputName', 'like', '%' . $inputName . '%');
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

        $sql = "select * from $this->table where corporationId = ?";
        $items = DB::select(
            $sql,
            [
                $editId
            ]
        );
        
        return $items;

    }

            /**
     * 個人情報更新
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
            'corporationId' => $data['corporationId'],
            'inputName' => $data['inputName'],
            'dispName' => $data['dispName'],
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
            'caseSummary' => $data['caseSummary'],
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
     * 個人情報削除
     *
     * @param $data
     * @throws Exception
     */
    public function deleteData($data) {

        $this->begin();

        $query = DB::table($this->table);
        $query->where('corporationId', $data['editId']);
        $query->delete();

        $this->commit();

    }


}
