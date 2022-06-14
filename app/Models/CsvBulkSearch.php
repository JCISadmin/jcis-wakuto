<?php

namespace App\Models;

use Exception;

/**
 * 一括検索
  */
class CsvBulkSearch extends BulkSearch
{
    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    private array $pdfHeader = array(
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

    /**
     * 登記簿検索CSV出力
     *
     * @param $data
     * @throws Exception
     */
    public function makeCsvFomPdf($data)
    {

        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);

        $csvData = $this->makePrintDate($data);

        $fileName = $tMngBatchData['fileName'].'.csv';
        if (!file_exists(storage_path('app/bulkSearch/download'))) {
            mkdir(storage_path('app/bulkSearch/download'));
        }
        $filePath = storage_path('app/bulkSearch/download') . '/'. $fileName;


        $fp = fopen($filePath, 'w');
        fwrite($fp, "\xEF\xBB\xBF");

        fputcsv($fp, $this->pdfHeader);

        foreach ($csvData['searchData'] as $fileKey => $fileItem) {
            foreach ($fileItem['keyword'] as $item) {

                if ($item['type'] === '法人検索') {

                    if (count($fileItem['corporationList'][$item['listIndex']]) > 0) {
                        foreach ($fileItem['corporationList'][$item['listIndex']] as $resultKey => $resultItem) {
                            $lineAry = [
                                '法人名' . ($resultKey > 0 ? '(複数該当)' : ''),
                                $item['companyName'],
                                '\''.$item['corporateCode'],
                                $item['companyAddress'],
                                '',
                                '',
                                '',
                                $item['hitSign'] ?? '',
                                '',
                                $csvData['isHitSearch'][$fileKey] ? '○' : '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                $resultItem['dispName'], // 会社名
                                $resultItem['industry'], // 業種
                                '\''.$resultItem['corporateCode'], // 法人番号
                                $resultItem['tel'], // 所在地の電話番号
                                $resultItem['postCode'], // 当時郵便番号
                                $resultItem['address'], // 当時所在地
                                $resultItem['delegate'], // 当時代表者
                                $resultItem['businessOwner'], // 当時実質経営者
                                $resultItem['department'], // 当時実質経営者所属
                                $resultItem['formatCaseDate'], // 事案年月日
                                $resultItem['casePersonName'], // 事案個人名
                                $resultItem['disposalOffice'], // 処分官署
                                $resultItem['requireDivision'], // 要件区分
                                $resultItem['caseSummary'], // 事案概要
                            ];
                            fputcsv($fp, $lineAry);
                        }

                    } else {
                        $lineAry = [
                            '法人名',
                            $item['companyName'],
                            '\''.$item['corporateCode'],
                            $item['companyAddress'],
                            '',
                            '',
                            '',
                            $item['hitSign'] ?? '',
                            '',
                            $csvData['isHitSearch'][$fileKey] ? '○' : '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ];
                        fputcsv($fp, $lineAry);
                    }

                }

                if ($item['type'] === '個人検索') {

                    if (count($fileItem['personList'][$item['listIndex']]) > 0) {
                        foreach ($fileItem['personList'][$item['listIndex']] as $resultKey => $resultItem) {
                            $lineAry = [
                                '個人名' . ($resultKey > 0 ? '(複数該当)' : ''),
                                '',
                                '',
                                '',
                                $item['position'],
                                $item['personName'],
                                $item['personAddress'],
                                '',
                                $item['hitSign'] ?? '',
                                '',
                                $resultItem['dispName'], // 該当個人名
                                $resultItem['dispKana'], // 該当異名・かな
                                $resultItem['formatBirthday'], // 生年月日
                                $resultItem['age'], // 現年齢
                                $resultItem['postCode'], // 当時郵便番号
                                $resultItem['address'], // 当時住所
                                $resultItem['departmentJob'], // 当時所属・役職
                                $resultItem['department'], // 当時所属団体名
                                $resultItem['departmentAddress'], // 当時団体所在地
                                $resultItem['formatCaseDate'], // 事案年月日
                                $resultItem['caseAge'], // 当時年齢
                                $resultItem['disposalOffice'], // 処分官署
                                $resultItem['requireDivision'], // 要件区分
                                $resultItem['caseSummary'], // 事案概要
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                                '',
                            ];
                            fputcsv($fp, $lineAry);
                        }

                    } else {
                        $lineAry = [
                            '個人名',
                            '',
                            '',
                            '',
                            $item['position'],
                            $item['personName'],
                            $item['personAddress'],
                            '',
                            $item['hitSign'] ?? '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ];
                        fputcsv($fp, $lineAry);
                    }

                }

            }

        }

        fclose($fp);

    }

    /**
     * @param $data
     * @throws Exception
     */
    public function makeCsvFomCvs($data)
    {
        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);

        $fileName = $tMngBatchData['fileName'].'.csv';
        if (!file_exists(storage_path('app/bulkSearch/download'))) {
            mkdir(storage_path('app/bulkSearch/download'));
        }
        $filePath = storage_path('app/bulkSearch/download') . '/'. $fileName;

        $corporationListIndex = 0;
        $personListIndex = 0;

        foreach ($data['searchData']['keyword'] as $key => $item) {
            if ($item['type'] === "法人検索") {
                $data['searchData']['keyword'][$key]['listIndex'] = $corporationListIndex;
                if (!empty($data['searchData']['corporationList'][$corporationListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                }

                $corporationListIndex++;

            } else if ($item['type'] === "個人検索") {
                $data['searchData']['keyword'][$key]['listIndex'] = $personListIndex;
                if (!empty($data['searchData']['personList'][$personListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                }

                $personListIndex++;
            }
        }

        $fp = fopen($filePath, 'w');
        fwrite($fp, "\xEF\xBB\xBF");

        if ($data['searchData']['keyword'][0]['type'] == '法人検索') {
            fputcsv($fp, $this->corporationHeader);
        } else {
            fputcsv($fp, $this->personHeader);
        }

        foreach ($data['searchData']['keyword'] as $item) {
            if ($item['type'] === "法人検索") {
                if (count($data['searchData']['corporationList'][$item['listIndex']]) > 0) {
                    foreach ($data['searchData']['corporationList'][$item['listIndex']] as $resultKey => $resultItem) {
                        $lineAry = [
                            '法人名' . ($resultKey > 0 ? '(複数該当)' : ''),
                            $item['name'],
                            $item['hitSign'],
                            $resultItem['dispName'], // '該当法人名',
                            $resultItem['industry'], // 業種
                            '\''.$resultItem['corporateCode'], // 法人番号
                            $resultItem['tel'], // 所在地の電話番号
                            $resultItem['postCode'], // 当時郵便番号
                            $resultItem['address'], // 当時所在地
                            $resultItem['delegate'], // 当時代表者
                            $resultItem['businessOwner'], // 当時実質経営者
                            $resultItem['department'], // 当時実質経営者所属
                            $resultItem['formatCaseDate'], // 事案年月日
                            $resultItem['casePersonName'], // 事案個人名
                            $resultItem['disposalOffice'], // 処分官署
                            $resultItem['requireDivision'], // 要件区分
                            $resultItem['caseSummary'], // 事案概要

                        ];
                        fputcsv($fp, $lineAry);
                    }

                } else {
                    $lineAry = [
                        '法人名',
                        $item['name'],
                        $item['hitSign'] ?? '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                    fputcsv($fp, $lineAry);
                }

            }

            if ($item['type'] === "個人検索") {
                if (count($data['searchData']['personList'][$item['listIndex']]) > 0) {
                    foreach ($data['searchData']['personList'][$item['listIndex']] as $resultKey => $resultItem) {
                        $lineAry = [
                            '個人名' . ($resultKey > 0 ? '(複数該当)' : ''),
                            $item['name'],
                            $item['hitSign'] ?? '',
                            $resultItem['dispName'], // 該当個人名
                            $resultItem['dispKana'], // 該当異名・かな
                            $resultItem['formatBirthday'], // 生年月日
                            $resultItem['age'], // 現年齢
                            $resultItem['postCode'], // 当時郵便番号
                            $resultItem['address'], // 当時住所
                            $resultItem['departmentJob'], // 当時所属・役職
                            $resultItem['department'], // 当時所属団体名
                            $resultItem['departmentAddress'], // 当時団体所在地
                            $resultItem['formatCaseDate'], // 事案年月日
                            $resultItem['caseAge'], // 当時年齢
                            $resultItem['disposalOffice'], // 処分官署
                            $resultItem['requireDivision'], // 要件区分
                            $resultItem['caseSummary'], // 事案概要
                        ];
                        fputcsv($fp, $lineAry);

                    }
                } else {
                    $lineAry = [
                        '個人名',
                        $item['name'],
                        $item['hitSign'] ?? '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                    fputcsv($fp, $lineAry);
                }
            }
        }

        fclose($fp);

    }

}
