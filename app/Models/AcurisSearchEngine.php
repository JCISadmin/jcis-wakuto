<?php

namespace App\Models;

use App\Models\SearchResultTcpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


/**
 * 検索用モデル
 */
class AcurisSearchEngine extends BaseModel
{

    /**
     * 法人情報検索
     *
     * @param $companyId
     * @param $userId
     * @param $name
     * @param $datasets
     * @param $countries
     * @return array|bool
     */
    public function searchCompany($companyId, $userId, $name, $datasets, $countries): array|bool
    {
        if ($name == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 法人検索API
        $rtnAPI = $acurisModel->businessesSearch($name, $datasets, $countries);

        // API検索でエラーが発生した場合
        if ($rtnAPI === FALSE) {
            // エラー件数として検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::CHARGE_FLG_OFF);
            return FALSE;

        } else {
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::CHARGE_FLG_ON);
            return $rtnAPI;
        }
    }

    /**
     * 個人情報検索
     *
     * @param $companyId
     * @param $userId
     * @param $name
     * @param $datasets
     * @param $countries
     * @param $dob
     * @return array|bool
     */
    public function searchPerson($companyId, $userId, $name, $datasets, $countries, $dob): array|bool
    {
        if ($name == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 個人検索API
        $rtnAPI = $acurisModel->individualsSearch($name, $datasets, $countries, $dob);

        // API検索でエラーが発生した場合
        if ($rtnAPI === FALSE) {
            // エラー件数として検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::CHARGE_FLG_OFF);
            return FALSE;

        } else {
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::CHARGE_FLG_ON);
            return $rtnAPI;
        }
    }

    /**
     * 法人情報検索(詳細)
     *
     * @param $companyId
     * @param $userId
     * @param $resourceId
     * @return string|bool
     */
    public function lookupCompany($companyId, $userId, $resourceId): string|bool
    {
        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 詳細PDF保存先
        $tempName = $resourceId.'.pdf';
        $path = storage_path('app/acurisSearch/lookup/' .$companyId .'/'. $userId. '/' .$tempName);

        // 法人検索API
        $rtnAPI = $acurisModel->businesssesLookup($resourceId, $path);

        if ($rtnAPI === TRUE) {
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::CHARGE_FLG_ON);
            $ret = $path;

        } else {
            // エラー件数として検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::CHARGE_FLG_OFF);
            $ret = FALSE;
        }

        return $ret;
    }

    /**
     * 個人情報検索(詳細)
     *
     * @param $companyId
     * @param $userId
     * @param $resourceId
     * @return string|bool
     */
    public function lookupPerson($companyId, $userId, $resourceId): string|bool
    {
        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 詳細PDF保存先
        $tempName = $resourceId.'.pdf';
        $path = storage_path('app/acurisSearch/lookup/' .$companyId .'/'. $userId. '/' .$tempName);

        // 個人検索API
        $rtnAPI = $acurisModel->individualsLookup($resourceId, $path);

        if ($rtnAPI === TRUE) {
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::CHARGE_FLG_ON);
            $ret = $path;

        } else {
            // エラー件数として検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::CHARGE_FLG_OFF);
            $ret = FALSE;
        }

        return $ret;
    }

    /**
     * PDFファイル名を取得
     *
     * @return string
     */
    public function getPdfFileName(): string
    {
        $pdfName = '検索結果-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
    }

    /**
     * 計算結果PDFを取得
     *
     * @param $pdfData
     * @param $fileName
     * @return string
     */
    public function makePdf($pdfData, $fileName): string
    {

        // PDF生成
        $pdfTemplate = 'pdf.pdfAcurisSearch';
        $pdf = new SearchResultTcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();

        $pdf->SetFont('ipamjm', 'B', 15);
        $pdf->Text(10, 15, "Acuris 検索",0.3, false, true, 0, 0, 'C');
        //タイトル下幅調整
        $pdf->Text(0, 20, "　");
        $pdf->SetFont('ipamjm', '', 9);

        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output($fileName, "S" );
    }

    /**
     * Excelファイル名を取得
     *
     * @return string
     */
    public function getExcelFileName(): string
    {
        $pdfName = '検索結果-%s.xlsx';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return $fileName;
    }

    /**
     * 計算結果EXCELをダウンロード
     *
     * @param $excelData
     * @param $fileName
     * @return bool
     */
    public function downloadExcel($excelData, $fileName): bool
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        //A~F列定義
        $sheet->setCellValue('A1', 'Name(氏名)');
        $sheet->setCellValue('B1', 'Date of Birth(生年月日)');
        $sheet->setCellValue('C1', 'DataSets(データセット)');
        $sheet->setCellValue('D1', 'Gender(性別)');
        $sheet->setCellValue('E1', 'Nationality(国籍)');
        $sheet->setCellValue('F1', 'Score(スコア)');
        $sheet->getColumnDimension('A')->setWidth(36.00);
        $sheet->getColumnDimension('B')->setWidth(24.00);
        $sheet->getColumnDimension('C')->setWidth(36.00);
        $sheet->getColumnDimension('D')->setWidth(18.00);
        $sheet->getColumnDimension('E')->setWidth(24.00);
        $sheet->getColumnDimension('F')->setWidth(18.00);

        // データ行
        $raw = 2;
        foreach ($excelData['result'] as $item) {

            $sheet->setCellValue('A'.$raw, $item['name']);
            $sheet->setCellValue('B'.$raw, isset($item['datesOfBirth']) ? $item['datesOfBirth'] : '');
            $sheet->setCellValue('C'.$raw, $item['datasets']);
            $sheet->setCellValue('D'.$raw, isset($item['gender']) ? $item['gender'] : '');
            $sheet->setCellValue('E'.$raw, $item['countries']);
            $sheet->setCellValue('F'.$raw, $item['score']);

            $raw++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;

        return true;
    }

    /**
     * 詳細検索 ZIPファイル名を取得
     *
     * @return string
     */
    public function getZipFileName(): string
    {
        $pdfName = 'acurisLookup-%s.zip';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return $fileName;
    }

    /**
     * 詳細検索 ログファイルを作成
     * @param $companyId
     * @param $userId
     * @param $resourceIds
     * @return string
     */
    public function makeLookupLogFile($companyId, $userId, $resourceIds): string
    {
        $logFilePath = storage_path('app/acurisSearch/lookup/'  .$companyId .'/'. $userId. '/README' .'.md');

        // ログファイルを作成
        touch($logFilePath);

        // 書き込み
        $fp = fopen($logFilePath, 'w');
        foreach ($resourceIds as $key => $item) {
            if ($item['status'] === TRUE) {
                fwrite($fp, sprintf('%d.結果: 成功 検索名: %s resourceId: %s' , $key+1, $item['name'], $item['resourceId'])."\n");
            } else {
                fwrite($fp, sprintf('%d 結果: 失敗 検索名: %s resourceId: %s' , $key+1, $item['name'], $item['resourceId'])."\n");
            }
        }
        return $logFilePath;
    }

}