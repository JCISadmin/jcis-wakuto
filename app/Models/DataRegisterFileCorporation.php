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
class DataRegisterFileCorporation extends BaseModel
{
    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    const CSV_IDX_NO = 0;
    const CSV_IDX_INPUT_NAME = 1;
    const CSV_IDX_DISP_NAME = 2;
    const CSV_IDX_INDUSTRY = 3;
    const CSV_IDX_POST_CODE = 4;
    const CSV_IDX_ADDRESS = 5;
    const CSV_IDX_CORPORATE_CODE = 6;
    const CSV_IDX_TEL = 7;
    const CSV_IDX_REQUIRE_DIVISION = 8;
    const CSV_IDX_BUSINESS_OWNER = 9;
    const CSV_IDX_DEPARTMENT = 10;
    const CSV_IDX_DELEGATE = 11;
    const CSV_IDX_CASE_PERSON_NAME = 12;
    const CSV_IDX_CASE_DATE = 13;
    const CSV_IDX_CASE_SUMMARY = 14;
    const CSV_IDX_DISPOSAL_OFFICE = 15;
    const CSV_IDX_INFO_KIND = 16;
    const CSV_IDX_INFO_SOURCE = 17;
    const CSV_IDX_FILE_NAME = 18;
    const CSV_IDX_REG_DATE = 19;
    const CSV_IDX_NOTE = 20;

    private array $errorMsg = [
        1 => 'CSVフォーマットエラー(No)',
        2 => 'CSVフォーマットエラー(法人・団体名(入力用))',
        3 => 'CSVフォーマットエラー(法人・団体名(表示用))',
        4 => 'CSVフォーマットエラー(業種)',
        5 => 'CSVフォーマットエラー(当時郵便番号)',
        6 => 'CSVフォーマットエラー(当時団体所在地)',
        7 => 'CSVフォーマットエラー(法人番号)',
        8 => 'CSVフォーマットエラー(所在地の電話番号)',
        9 => 'CSVフォーマットエラー(要件区分)',
        10 => 'CSVフォーマットエラー(当時実質経営者)',
        11 => 'CSVフォーマットエラー(実質経営者所属)',
        12 => 'CSVフォーマットエラー(当時代表者)',
        13 => 'CSVフォーマットエラー(事案個人名)',
        14 => 'CSVフォーマットエラー(事案年月日)',
        15 => 'CSVフォーマットエラー(事案概要)',
        16 => 'CSVフォーマットエラー(処分官署)',
        17 => 'CSVフォーマットエラー(情報種別)',
        18 => 'CSVフォーマットエラー(情報ソース)',
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

        $model = new MCorporation();

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

        // INPUT NAME 桁数80
        if ($data[self::CSV_IDX_INPUT_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INPUT_NAME]) > 80) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INPUT_NAME],
                    'errId' => 2
                );
                $this->rollback();
                return false;
            }
        }

        // DISP NAME 桁数80
        if ($data[self::CSV_IDX_DISP_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DISP_NAME]) > 80) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DISP_NAME],
                    'errId' => 3
                );
                $this->rollback();
                return false;
            }
        }



        // INDUSTRY 文字数60
        if ($data[self::CSV_IDX_INDUSTRY] !== '') {
            if (mb_strlen($data[self::CSV_IDX_INDUSTRY]) > 60) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_INDUSTRY],
                    'errId' => 4
                );
                $this->rollback();
                return false;
            }
        }


        // POST CODE 半角数 桁数8
        if ($data[self::CSV_IDX_POST_CODE] !== '') {
            if (!preg_match("/^[0-9]{3}[-]?[0-9]{4}$/", $data[self::CSV_IDX_POST_CODE]) || strlen($data[self::CSV_IDX_POST_CODE]) > 8) {
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
            if (mb_strlen($data[self::CSV_IDX_ADDRESS]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_ADDRESS],
                    'errId' => 6
                );
                $this->rollback();
                return false;

            }
        }

        // CORPORATE CODE 文字数20
        if ($data[self::CSV_IDX_CORPORATE_CODE]  !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_CORPORATE_CODE]) || strlen($data[self::CSV_IDX_CORPORATE_CODE]) > 20) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CORPORATE_CODE],
                    'errId' => 7
                );
                $this->rollback();
                return false;

            }
        }

        // TEL 文字数20
        if ($data[self::CSV_IDX_TEL] !== '') {
            if (!preg_match("/^[0-9\/]+$/", $data[self::CSV_IDX_TEL]) || strlen($data[self::CSV_IDX_TEL]) > 20) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_TEL],
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

        // BUSINESS OWNER 文字数20
        if ($data[self::CSV_IDX_BUSINESS_OWNER] !== '') {
            if (mb_strlen($data[self::CSV_IDX_BUSINESS_OWNER]) > 20) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_BUSINESS_OWNER],
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

        // DELEGATE 文字数20
        if ($data[self::CSV_IDX_DELEGATE] !== '') {
            if (mb_strlen($data[self::CSV_IDX_DELEGATE]) > 20) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_DELEGATE],
                    'errId' => 12
                );
                $this->rollback();
                return false;

            }
        }

        // CASE PERSON NAME 文字数20
        if ($data[self::CSV_IDX_CASE_PERSON_NAME] !== '') {
            if (mb_strlen($data[self::CSV_IDX_CASE_PERSON_NAME]) > 20) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_PERSON_NAME],
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
        if ($data[self::CSV_IDX_CASE_DATE] === '') {
            $data[self::CSV_IDX_CASE_DATE] = null;
        }


        // CASE SUMMARY 文字数200
        if ($data[self::CSV_IDX_CASE_SUMMARY] !== '') {
            if (mb_strlen($data[self::CSV_IDX_CASE_SUMMARY]) > 200) {
                $this->errorInfo[] = array(
                    'row' => $rawCnt,
                    'no' => $data[self::CSV_IDX_CASE_SUMMARY],
                    'errId' => 15
                );
                $this->rollback();
                return false;

            }
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
            'industry' => $data[self::CSV_IDX_INDUSTRY],
            'postCode' => $data[self::CSV_IDX_POST_CODE],
            'address' => $data[self::CSV_IDX_ADDRESS],
            'corporateCode' => $data[self::CSV_IDX_CORPORATE_CODE],
            'tel' => $data[self::CSV_IDX_TEL],
            'requireDivision' => $data[self::CSV_IDX_REQUIRE_DIVISION],
            'businessOwner' => $data[self::CSV_IDX_BUSINESS_OWNER],
            'department' => $data[self::CSV_IDX_DEPARTMENT],
            'delegate' => $data[self::CSV_IDX_DELEGATE],
            'casePersonName' => $data[self::CSV_IDX_CASE_PERSON_NAME],
            'caseDate' => $data[self::CSV_IDX_CASE_DATE],
            'caseSummary' => $data[self::CSV_IDX_CASE_SUMMARY],
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
