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
class DataRegisterFilePerson extends BaseModel
{
    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    const CSV_IDX_NO = 0;
    const CSV_IDX_INPUT_NAME = 1;
    const CSV_IDX_DISP_NAME = 2;
    const CSV_IDX_INPUT_KANA = 3;
    const CSV_IDX_DISP_KANA = 4;
    const CSV_IDX_BIRTHDAY = 5;
    const CSV_IDX_POST_CODE = 6;
    const CSV_IDX_ADDRESS = 7;
    const CSV_IDX_REQUIRE_DIVISION = 8;
    const CSV_IDX_DEPARTMENT_JOB = 9;
    const CSV_IDX_DEPARTMENT = 10;
    const CSV_IDX_DEPARTMENT_ADDRESS = 11;
    const CSV_IDX_CASE_DATE = 12;
    const CSV_IDX_CASE_SUMMARY = 13;
    const CSV_IDX_CASE_AGE = 14;
    const CSV_IDX_DISPOSAL_OFFICE = 15;
    const CSV_IDX_INFO_KIND = 16;
    const CSV_IDX_INFO_SOURCE = 17;
    const CSV_IDX_FILE_NAME = 18;
    const CSV_IDX_REG_DATE = 19;
    const CSV_IDX_NOTE = 20;



    private array $errorMsg = [
        1 => 'CSVフォーマットエラー(No)',
        2 => 'CSVフォーマットエラー(氏名(入力用))',
        3 => 'CSVフォーマットエラー(氏名(表示用))',
        4 => 'CSVフォーマットエラー(異名・かな(入力用))',
        5 => 'CSVフォーマットエラー(異名・かな(表示用))',
        6 => 'CSVフォーマットエラー(生年月日)',
        7 => 'CSVフォーマットエラー(当時郵便番号)',
        8 => 'CSVフォーマットエラー(当時住所)',
        9 => 'CSVフォーマットエラー(要件区分)',
        10 => 'CSVフォーマットエラー(当時所属・役職)',
        11 => 'CSVフォーマットエラー(実質所属団体名)',
        12 => 'CSVフォーマットエラー(当時団体所在地)',
        13 => 'CSVフォーマットエラー(事案年月日)',
        14 => 'CSVフォーマットエラー(事案概要)',
        15 => 'CSVフォーマットエラー(当時年齢)',
        16 => 'CSVフォーマットエラー(処分官署)',
        17 => 'CSVフォーマットエラー(情報ソース)',
        18 => 'CSVフォーマットエラー(情報種別)',
        19 => 'CSVフォーマットエラー(ファイル名)',
        20 => 'CSVフォーマットエラー(登録日)',
        21 => 'CSVフォーマットエラー(備考)',
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
    public function import($fileName): array
    {

        $this->errorInfo = array();

        if (file_exists($fileName) === false) {
            $fileName = basename($fileName);
            throw new VaildException("ファイルが見つかりません($fileName)");
        }

        $fp = fopen($fileName, "r");
        if($fp === false) {
            $fileName = basename($fileName);
            throw new VaildException("$fileName is fopen error.");
        }

        $header = fgetcsv($fp, 0);
        if($header === false) {
            fclose($fp);
            $fileName = basename($fileName);
            throw new VaildException("$fileName is fgetcsv error.");
        }


        if (count($header) !== 21) {
            fclose($fp);
            $fileName = basename($fileName);
            throw new VaildException("ヘッダーが無効な形式です($fileName)");
        }

        $rawCnt = 0;

        $cntAry['rawCnt'] = 0; // 処理件数
        $cntAry['sucCnt'] = 0; // 処理成功件数

        while (($data = fgetcsv($fp, 0)) !== false) {
            $cntAry['rawCnt']++;
            if ($this->updateData($data, $rawCnt) === true) {
                $cntAry['sucCnt']++;
            }

            if($cntAry['rawCnt'] > 5000){
                throw new VaildException("データが5000件以上あります。($fileName)");
            }
        }

        fclose($fp);

        foreach ($this->errorInfo as $key => $value) {
            $this->errorInfo[$key]['errMsg'] = $this->errorMsg[$value['errId']];
        }

        return $cntAry;

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

        $model = new MPerson();

        // NO 半角英数 桁数10
        if ($data[self::CSV_IDX_NO] !== '') {
            if (strlen($data[self::CSV_IDX_NO]) > 10) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_NO],
                    'errId' => 1
                );
                $this->rollback();
                return false;
            }
        }

        // INPUT NAME 半角英数 桁数130
        if ($data[self::CSV_IDX_INPUT_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INPUT_NAME]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INPUT_NAME],
                    'errId' => 2
                );
                $this->rollback();
                return false;
            }
        }

        // DISP NAME 半角英数 桁数130
        if ($data[self::CSV_IDX_DISP_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DISP_NAME]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DISP_NAME],
                    'errId' => 3
                );
                $this->rollback();
                return false;
            }
        }

        // INPUT KANA 半角英数 桁数130
        if ($data[self::CSV_IDX_INPUT_KANA] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INPUT_KANA]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INPUT_KANA],
                    'errId' => 4
                );
                $this->rollback();
                return false;
            }
        }

        // DISP KANA 半角英数 桁数130
        if ($data[self::CSV_IDX_DISP_KANA] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DISP_KANA]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DISP_KANA],
                    'errId' => 5
                );
                $this->rollback();
                return false;
            }
        }

        // BIRTHDAY DATE
        if ($data[self::CSV_IDX_BIRTHDAY] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_BIRTHDAY]) || $this->checkDate($data[self::CSV_IDX_BIRTHDAY])) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_BIRTHDAY],
                    'errId' => 6
                );
                $this->rollback();
                return false;
            }
        }
        if ($data[self::CSV_IDX_BIRTHDAY] === '') {
            $data[self::CSV_IDX_BIRTHDAY] = null;
        }


        // POST CODE 半角数 桁数8
        if ($data[self::CSV_IDX_POST_CODE] !== '') {
            if (!preg_match("/^[0-9]{3}[-]?[0-9]{4}$/", $data[self::CSV_IDX_POST_CODE]) || strlen($data[self::CSV_IDX_POST_CODE]) > 8) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_POST_CODE],
                    'errId' => 7
                );
                $this->rollback();
                return false;
            }
        }

        // ADDRESS 文字数200
        if ($data[self::CSV_IDX_ADDRESS] !== '') {
            if (mb_strlen($data[self::CSV_IDX_ADDRESS]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_ADDRESS],
                    'errId' => 8
                );
                $this->rollback();
                return false;

            }
        }

        // REQUIRE DIVISION 文字数50
        if ($data[self::CSV_IDX_REQUIRE_DIVISION] !== '') {
            if (mb_strlen($data[self::CSV_IDX_REQUIRE_DIVISION]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_REQUIRE_DIVISION],
                    'errId' => 9
                );
                $this->rollback();
                return false;

            }
        }

        // DEPARTMENT JOB 文字数50
        if ($data[self::CSV_IDX_DEPARTMENT_JOB] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DEPARTMENT_JOB]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DEPARTMENT_JOB],
                    'errId' => 10
                );
                $this->rollback();
                return false;

            }
        }

        // DEPARTMENT 文字数50
        if ($data[self::CSV_IDX_DEPARTMENT] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DEPARTMENT]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DEPARTMENT],
                    'errId' => 11
                );
                $this->rollback();
                return false;

            }
        }

        // DEPARTMENT ADDRESS 文字数200
        if ($data[self::CSV_IDX_DEPARTMENT_ADDRESS] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DEPARTMENT_ADDRESS]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DEPARTMENT_ADDRESS],
                    'errId' => 12
                );
                $this->rollback();
                return false;

            }
        }

        // CASE DATE DATE
        if ($data[self::CSV_IDX_CASE_DATE] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_CASE_DATE]) || $this->checkDate($data[self::CSV_IDX_CASE_DATE])) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_DATE],
                    'errId' => 13
                );
                $this->rollback();
                return false;

            }
        }
        if ($data[self::CSV_IDX_CASE_DATE] === '') {
            $data[self::CSV_IDX_CASE_DATE] = null;
        }


        // CASE SUMMARY 文字数200
        if ($data[self::CSV_IDX_CASE_SUMMARY] !== '') {
            if (mb_strlen($data[self::CSV_IDX_CASE_SUMMARY]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_SUMMARY],
                    'errId' => 14
                );
                $this->rollback();
                return false;

            }
        }

        // CASE AGE 文字数11
        if ($data[self::CSV_IDX_CASE_AGE] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_CASE_AGE]) || strlen($data[self::CSV_IDX_CASE_AGE]) > 11) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_AGE],
                    'errId' => 15
                );
                $this->rollback();
                return false;
            }
        }
        if ($data[self::CSV_IDX_CASE_AGE] === '') {
            $data[self::CSV_IDX_CASE_AGE] = null;
        }





        // DISPOSAL OFFICE 文字数50
        if ($data[self::CSV_IDX_DISPOSAL_OFFICE] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DISPOSAL_OFFICE]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DISPOSAL_OFFICE],
                    'errId' => 16
                );
                $this->rollback();
                return false;

            }
        }

        // INFO KIND 文字数50
        if ($data[self::CSV_IDX_INFO_KIND] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INFO_KIND]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INFO_KIND],
                    'errId' => 17
                );
                $this->rollback();
                return false;

            }
        }

        // INFO SOURCE 文字数50
        if ($data[self::CSV_IDX_INFO_SOURCE] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INFO_SOURCE]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INFO_SOURCE],
                    'errId' => 18
                );
                $this->rollback();
                return false;

            }
        }

        // FILE NAME 文字数80
        if ($data[self::CSV_IDX_FILE_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_FILE_NAME]) > 80) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_FILE_NAME],
                    'errId' => 19
                );
                $this->rollback();
                return false;

            }
        }

        // REG DATE YYYY/MM/DD
        if ($data[self::CSV_IDX_REG_DATE] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_REG_DATE]) || $this->checkDate($data[self::CSV_IDX_REG_DATE])) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_REG_DATE],
                    'errId' => 20
                );
                $this->rollback();
                return false;

            }
        }
        if ($data[self::CSV_IDX_REG_DATE] === '') {
            $data[self::CSV_IDX_REG_DATE] = null;
        }


        // NOTE 文字数200
        if ($data[self::CSV_IDX_NOTE] !== '') {
            if (mb_strlen($data[self::CSV_IDX_NOTE]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_NOTE],
                    'errId' => 21
                );
                $this->rollback();
                return false;

            }
        }


        $insData = [
            'inputName' => $data[self::CSV_IDX_INPUT_NAME],
            'dispName' => $data[self::CSV_IDX_DISP_NAME],
            'inputKana' => $data[self::CSV_IDX_INPUT_KANA],
            'dispKana' => $data[self::CSV_IDX_DISP_KANA],
            'birthday' => $data[self::CSV_IDX_BIRTHDAY],
            'postCode' => $data[self::CSV_IDX_POST_CODE],
            'address' => $data[self::CSV_IDX_ADDRESS],
            'requireDivision' => $data[self::CSV_IDX_REQUIRE_DIVISION],
            'departmentJob' => $data[self::CSV_IDX_DEPARTMENT_JOB],
            'department' => $data[self::CSV_IDX_DEPARTMENT],
            'departmentAddress' => $data[self::CSV_IDX_DEPARTMENT_ADDRESS],
            'caseDate' => $data[self::CSV_IDX_CASE_DATE],
            'caseSummary' => $data[self::CSV_IDX_CASE_SUMMARY],
            'caseAge' => $data[self::CSV_IDX_CASE_AGE],
            'disposalOffice' => $data[self::CSV_IDX_DISPOSAL_OFFICE],
            'infoKind' => $data[self::CSV_IDX_INFO_KIND],
            'infoSource' => $data[self::CSV_IDX_INFO_SOURCE],
            'filename' => $data[self::CSV_IDX_FILE_NAME],
            'regDate' => $data[self::CSV_IDX_REG_DATE],
            'note' => $data[self::CSV_IDX_NOTE],
        ];

        $model->ins($insData);

        $this->commit();
        return true;
    }

}
