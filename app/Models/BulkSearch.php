<?php

namespace App\Models;

use Exception;
use App\Exceptions\VaildException;

/**
 * Class DataRegister
 *   ファイルインポート
 *
 * @package App\Models
 */
class BulkSearch extends BaseModel
{
    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //


    private array $errorMsg = [
        1 => '',
        2 => '',
        3 => '',
        4 => '',
        5 => '',
        6 => '',
        7 => '',
        8 => '',
        9 => '',
        10 => '',
        11 => '',
        12 => '',
        13 => '',
        14 => '',
        15 => '',
        16 => '',
        17 => '',
        18 => '',
        19 => '',
        20 => '',
        21 => '',
    ];

    public array $errorInfo;


    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * ファイル取込処理
     *
     * @param $fileName
     * @return array
     * @throws VaildException|Exception
     */
    public function import(): array
    {

        $this->errorInfo = array();



    }


    // ---------------------------------------------------------------- //
    // ----------------------- Methods protected ---------------------- //
    // ---------------------------------------------------------------- //

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Private ------------------------ //
    // ---------------------------------------------------------------- //

    /**
     * 行単位更新処理
     *
     * @param $data
     * @param $rawCnt
     * @return bool
     * @throws Exception
     */
    private function updateData($data, $rawCnt): bool
    {

        $this->begin();



        $insData = [
        ];

        $model->ins($insData);

        $this->commit();
        return true;
    }

    /**
     * CSV生成
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Throwable
     */
    public function makeCSV(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $items = $request->all();

        $header = [
            'No',
            '法人・団体名(入力用)',
            '法人・団体名(表示用)',
            '業種',
            '当時郵便番号',
            '当時団体所在地',
            '法人番号',
            '所在地の電話番号',
            '要件区分',
            '当時実質経営者',
            '実質経営者所属',
            '当時代表者',
            '事案個人名',
            '事案年月日',
            '事案概要',
            '処分官署',
            '情報種別',
            '情報ソース',
            'ファイル名',
            '登録日',
            '備考',
        ];

        $data = [

        ];

        $fp = fopen('bulkSearch.csv', 'w');

        mb_convert_variables('SJIS', 'UTF-8', $header);
        fputcsv($fp, $header);

        foreach ($users as $user) {
            mb_convert_variables('SJIS', 'UTF-8', $user);
            fputcsv($fp, $user);
        }

    }




}
