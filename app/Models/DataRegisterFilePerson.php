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
        2 => 'CSVフォーマットエラー(inputName)',
        3 => 'CSVフォーマットエラー(dispName)',
        4 => 'CSVフォーマットエラー(inputKana)',
        5 => 'CSVフォーマットエラー(dispKana)',
        6 => 'CSVフォーマットエラー(birthday)',
        7 => 'CSVフォーマットエラー(postCode)',
        8 => 'CSVフォーマットエラー(address)',
        9 => 'CSVフォーマットエラー(requireDivision)',
        10 => 'CSVフォーマットエラー(departmentJob)',
        11 => 'CSVフォーマットエラー(department)',
        12 => 'CSVフォーマットエラー(departmentAddress)',
        13 => 'CSVフォーマットエラー(caseDate)',
        14 => 'CSVフォーマットエラー(caseSummary)',
        15 => 'CSVフォーマットエラー(caseAge)',
        16 => 'CSVフォーマットエラー(disposalOffice)',
        17 => 'CSVフォーマットエラー(infoKind)',
        18 => 'CSVフォーマットエラー(infoSource)',
        19 => 'CSVフォーマットエラー(filename)',
        20 => 'CSVフォーマットエラー(regDate)',
        21 => 'CSVフォーマットエラー(note)',
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
        $header = fgetcsv($fp, 0);

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
                break;
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
            if (strlen($data[self::CSV_IDX_INPUT_NAME]) > 130) {
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
            if (strlen($data[self::CSV_IDX_DISP_NAME]) > 130) {
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
            if (strlen($data[self::CSV_IDX_INPUT_KANA]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INPUT_KANA],
                    'errId' => 2
                );
                $this->rollback();
                return false;
            }
        }

        // DISP KANA 半角英数 桁数130
        if ($data[self::CSV_IDX_DISP_KANA] !== '') {
            if (strlen($data[self::CSV_IDX_DISP_KANA]) > 130) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DISP_KANA],
                    'errId' => 3
                );
                $this->rollback();
                return false;
            }
        }

        // BIRTHDAY YYYY/MM/DD
        if ($data[self::CSV_IDX_BIRTHDAY] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_BIRTHDAY]) || $this->checkDate($data[self::CSV_IDX_BIRTHDAY])) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_BIRTHDAY],
                    'errId' => 4
                );
                $this->rollback();
                return false;
            }
        }


        // POST CODE 半角数 桁数7
        if ($data[self::CSV_IDX_POST_CODE] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_POST_CODE]) || strlen($data[self::CSV_IDX_POST_CODE]) > 8) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_POST_CODE],
                    'errId' => 5
                );
                $this->rollback();
                return false;
            }
        }

        // ADDRESS 文字数200
        if ($data[self::CSV_IDX_ADDRESS] !== '') {
            if (strlen($data[self::CSV_IDX_ADDRESS]) > 200) {
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
            if (strlen($data[self::CSV_IDX_REQUIRE_DIVISION]) > 20) {
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
            if (strlen($data[self::CSV_IDX_DEPARTMENT_JOB]) > 50) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DEPARTMENT_JOB],
                    'errId' => 12
                );
                $this->rollback();
                return false;

            }
        }

        // DEPARTMENT 文字数50
        if ($data[self::CSV_IDX_DEPARTMENT] !== '') {
            if (strlen($data[self::CSV_IDX_DEPARTMENT]) > 50) {
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
            if (strlen($data[self::CSV_IDX_DEPARTMENT_ADDRESS]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DEPARTMENT_ADDRESS],
                    'errId' => 13
                );
                $this->rollback();
                return false;

            }
        }

        // CASE DATE YYYY/MM/DD
        if ($data[self::CSV_IDX_CASE_DATE] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_CASE_DATE]) || $this->checkDate($data[self::CSV_IDX_CASE_DATE])) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_DATE],
                    'errId' => 14
                );
                $this->rollback();
                return false;

            }
        }

        // CASE SUMMARY 文字数200
        if ($data[self::CSV_IDX_CASE_SUMMARY] !== '') {
            if (strlen($data[self::CSV_IDX_CASE_SUMMARY]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_SUMMARY],
                    'errId' => 15
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




        // DISPOSAL OFFICE 文字数50
        if ($data[self::CSV_IDX_DISPOSAL_OFFICE] !== '') {
            if (strlen($data[self::CSV_IDX_DISPOSAL_OFFICE]) > 50) {
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
            if (strlen($data[self::CSV_IDX_INFO_KIND]) > 50) {
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
            if (strlen($data[self::CSV_IDX_INFO_SOURCE]) > 50) {
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
            if (strlen($data[self::CSV_IDX_FILE_NAME]) > 80) {
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

        // NOTE 文字数200
        if ($data[self::CSV_IDX_NOTE] !== '') {
            if (strlen($data[self::CSV_IDX_NOTE]) > 200) {
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
