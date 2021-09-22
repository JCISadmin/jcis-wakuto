<?php

namespace App\Models;

use DateTime;
use Exception;
use App\Models\BulkSearch;

/**
 * 一括検索
  */
class CsvBulkSearch extends BaseModel
{
     // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $personCorporationHeader = array(
        '個人／法人名',
        '会社名',
        '法人番号',
        '会社住所',
        '構成員役職名',
        '役員名',
        '代表取締役住所',
        '結果(法)',
        '結果(個)',
        '結果(法＋個)',
        '該当個人名',
        '該当異名・かな',
        '生年月日',
        '現年齢',
        '当時郵便番号',
        '当時住所',
        '当時所属・役職',
        '当時所属団体名',
        '当時団体所在地',
        '事案年月日',
        '当時年齢',
        '処分官署',
        '要件区分',
        '事案概要',
        '該当法人名',
        '業種',
        '法人番号',
        '所在地の電話番号',
        '当時郵便番号',
        '当時所在地',
        '当時代表者',
        '当時実質経営者',
        '当時実質経営者所属',
        '事案年月日',
        '事案個人名',
        '処分官署',
        '要件区分',
        '事案概要',
    );


    private array $personHeader = array(
        '個人名',
        '抽出個人名',
        '結果(個)',
        '該当個人名',
        '該当異名・かな',
        '生年月日',
        '現年齢',
        '当時郵便番号',
        '当時住所',
        '当時所属・役職',
        '当時所属団体名',
        '当時団体所在地',
        '事案年月日',
        '当時年齢',
        '処分官署',
        '要件区分',
        '事案概要',
    );

    private array $corporationHeader = array(
        '法人名',
        '抽出法人名',
        '結果(法)',
        '該当法人名',
        '業種',
        '法人番号',
        '所在地の電話番号',
        '当時郵便番号',
        '当時所在地',
        '当時代表者',
        '当時実質経営者',
        '当時実質経営者所属',
        '事案年月日',
        '事案個人名',
        '処分官署',
        '要件区分',
        '事案概要',
    );

    const CSV_BULK_SEARCH = 'app/bulkSearch/pdf';
    const CORPORATION_SEARCH = '法人名';
    const PEASON_SEARCH = '個人名';
    const MULTI_HIT_COMMENT = '(複数該当)';
    const HIT = '〇';

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     *
     * @param $fileType
     * @param $fileName
     * @param $data
     * @throws Exception
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function makeCsv($companyId, $batchId, $fileType, $data)
    {
        $model = new BulkSearch();
        
        //CSVを保存するフォルダを作成
        if(file_exists(storage_path(self::CSV_BULK_SEARCH)) === false){
            mkdir(storage_path(self::CSV_BULK_SEARCH), '0777');
        }

        //ファイル名
        $dt = new DateTime();
        $uploadTime = date_format($dt,'YmdHis');
        $batchData = $model->getData($companyId, $batchId);
        $fileName = $batchData->fileName.'_'.$uploadTime.'.csv';
        //ファイルパス
        $filePath = storage_path(self::CSV_BULK_SEARCH.'/').$fileName;

        switch($fileType){
            //登記簿
            case 'application/pdf':
            case 'application/zip':
                $this->registryToCsv($filePath, $data);
                break;

            default:
                break;
        }

    }

    /**
     *
     * @param $filePath
     * @param $data
     * @throws Exception
     *
     * @noinspection PhpArrayShapeAttributeCanBeAddedInspection
     */
    public function registryToCsv($filePath, $data)
    {

        $fp = fopen($filePath, 'w');
        fputcsv($fp, $this->personCorporationHeader);

        
        $corporationAry[] = null;
        $cIndex = 0;
        $peasonAry[] = null;
        $pIndex = 0;
        
        foreach($data[0] as $saerchItem){
            if($saerchItem[1] === self::CORPORATION_SEARCH){
                $corporationAry[$cIndex] = $saerchItem;
                $cIndex++;
            }elseif($saerchItem[1] === self::PEASON_SEARCH){
                $peasonAry[$pIndex] = $saerchItem;
                $pIndex++;
            }
        }

        $hitCount = 0;
        //法人検索のヒット数
        foreach($data[1] as $item){
            if(is_null($item) === false){
                $hitCount++;
            }
        }

        //個人検索のヒット数
        foreach($data[2] as $item){
            if(is_null($item) === false){
                $hitCount++;
            }
        }

        //法人検索
        if(is_null($corporationAry) === false){
            foreach($corporationAry as $key => $value){
                if($data[1][$key] !== []){
                    foreach($data[1][$key] as $subKey => $resultItem){
                        $comment = '';
                        if($subKey > 0){
                            $comment = self::MULTI_HIT_COMMENT;
                        }
                        $row = [
                            $value[1].$comment,
                            $value[2],
                            $value[3],
                            $value[4],
                            $data[1][$key] === [] ? null : self::HIT,
                            '',// 結果(個)
                            $hitCount > 0 ? null : self::HIT,
                            '',// 該当個人名
                            '',// 該当異名・かな
                            '',// 生年月日
                            '',// 現年齢
                            '',// 当時郵便番号
                            '',// 当時住所
                            '',// 当時所属・役職
                            '',// 当時所属団体名
                            '',// 当時団体所在地
                            '',// 事案年月日
                            '',// 当時年齢
                            '',// 処分官署
                            '',// 要件区分
                            '',// 事案概要
                            $resultItem === [] ? '' : $resultItem['dispName'],
                            $resultItem === [] ? '' : $resultItem['industry'],
                            $resultItem === [] ? '' : $resultItem['corporateCode'],
                            $resultItem === [] ? '' : $resultItem['tel'],
                            $resultItem === [] ? '' : $resultItem['postCode'],
                            $resultItem === [] ? '' : $resultItem['address'],
                            $resultItem === [] ? '' : $resultItem['delegate'],
                            $resultItem === [] ? '' : $resultItem['businessOwner'],
                            $resultItem === [] ? '' : $resultItem['department'],
                            $resultItem === [] ? '' : $resultItem['caseDate'],
                            $resultItem === [] ? '' : $resultItem['casePersonName'],
                            $resultItem === [] ? '' : $resultItem['disposalOffice'],
                            $resultItem === [] ? '' : $resultItem['requireDivision'],
                            $resultItem === [] ? '' : $resultItem['caseSummary'],
                        ];
                        fputcsv($fp, $row);
                    }
                }
            }
        }

        //個人検索
        if(is_null($peasonAry) === false){
            foreach($corporationAry as $key => $value){
                if(is_null($data[2][$key]) === false){
                    foreach($data[2][$key] as $subKey => $resultItem){

                        $comment = '';
                        if($subKey > 0){
                            $comment = self::MULTI_HIT_COMMENT;
                        }

                        $row = [
                            $value[1].$comment,
                            $value[2],
                            $value[3],
                            $value[4],
                            '',// 結果(法)
                            $data[2][$key] === null ? null : self::HIT,
                            '',// 結果(法＋個)
                            $resultItem === null ? null : $resultItem['dispName'],
                            $resultItem === null ? null : $resultItem['dispKana'],
                            $resultItem === null ? null : $resultItem['birthday'],
                            $resultItem === null ? null : $resultItem['age'],
                            $resultItem === null ? null : $resultItem['postCode'],
                            $resultItem === null ? null : $resultItem['address'],
                            $resultItem === null ? null : $resultItem['departmentJob'],
                            $resultItem === null ? null : $resultItem['department'],
                            $resultItem === null ? null : $resultItem['departmentAddress'],
                            $resultItem === null ? null : $resultItem['caseDate'],
                            $resultItem === null ? null : $resultItem['caseAge'],
                            $resultItem === null ? null : $resultItem['disposalOffice'],
                            $resultItem === null ? null : $resultItem['requireDivision'],
                            $resultItem === null ? null : $resultItem['caseSummary'],
                            '',// 該当法人名
                            '',// 業種
                            '',// 法人番号
                            '',// 所在地の電話番号
                            '',// 当時郵便番号
                            '',// 当時所在地
                            '',// 当時代表者
                            '',// 当時実質経営者
                            '',// 当時実質経営者所属
                            '',// 事案年月日
                            '',// 事案個人名
                            '',// 処分官署
                            '',// 要件区分
                            '',// 事案概要
                        ];

                        fputcsv($fp, $row);
                    }
                }
            }
        }
        fclose($fp);
    }
}
