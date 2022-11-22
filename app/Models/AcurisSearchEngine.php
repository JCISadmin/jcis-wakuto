<?php

namespace App\Models;

use Exception;
use Datetime;
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
     * @param $contractPlanId
     * @param $userId
     * @param $name
     * @param $city
     * @param $isFuzzy
     * @return array
     * @throws Exception
     */
    public function searchCompany($companyId, $userId, $name, $datasets, $countries): array
    {
        if ($name == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 法人検索API
        $rtnAPI = $acurisModel->businessesSearch($name, $datasets, $countries);

        $retList = [];

        // ステータスコードが200以上300未満か判定
        if($rtnAPI->ok()){
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::DETAIL_FLG_ON);

            // 検索結果を取得
            $resultJson = $rtnAPI->body();
            $result = json_decode($resultJson);

            // 検索結果有り
            if($result->results->matchCount > 0){
                foreach($result->results->matches as $resultItem){
                    $retList[] = (array)$resultItem;
                }
            }

        }else{
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::DETAIL_FLG_OFF);
        }


        return $retList;
    }

    /**
     * 個人情報検索
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $name
     * @param $city
     * @param $isFuzzy
     * @return array
     * @throws Exception
     */
    public function searchPerson($companyId, $userId, $name, $datasets, $countries, $dob): array
    {
        if ($name == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();

        // 個人検索API
        $rtnAPI = $acurisModel->individualsSearch($name, $datasets, $countries, $dob);

        $retList = [];

        // ステータスコードが200以上300未満か判定
        if($rtnAPI->ok()){
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::DETAIL_FLG_ON);

            // 検索結果を取得
            $resultJson = $rtnAPI->body();
            $result = json_decode($resultJson);

            // 検索結果有り
            if($result->results->matchCount > 0){
                foreach($result->results->matches as $resultItem){
                    $retList[] = (array)$resultItem;
                }
            }

        }else{
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_OFF, self::DETAIL_FLG_OFF);
        }

        return $retList;
    }

    /**
     * 法人情報検索(詳細)
     *
     * @param $companyId
     * @param $userId
     * @param $resourceId
     * @return string|null
     * @throws Exception
     */
    public function searchCompanyDetail($companyId, $userId, $resourceId): string|null
    {
        if ($resourceId == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();
        $rtnPath = NULL;

        // 詳細PDF保存先
        $date = new DateTime();
        $tempName = $resourceId.'_'.$date->format('Ymd_Hisu').'.pdf';
        $path = '/tmp/' . $tempName;

        // 法人検索API
        $rtnAPI = $acurisModel->businessesLookup($resourceId, $path);

        // ステータスコードが200以上300未満か判定
        if($rtnAPI->ok()){
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::DETAIL_FLG_ON);
            
            $rtnPath = $path;

        }else{
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::DETAIL_FLG_OFF);
        }

        return $rtnPath;
    }

    /**
     * 個人情報検索(詳細)
     *
     * @param $companyId
     * @param $userId
     * @param $resourceId
     * @return string|null
     * @throws Exception
     */
    public function searchPersonDetail($companyId, $userId, $resourceId): string|null
    {
        if ($resourceId == '') {
            return [];
        }

        $acurisModel = new AcurisSearch();
        $keywordModel = new TAcurisKeywordHistory();
        $rtnPath = NULL;

        // 詳細PDF保存先
        $date = new DateTime();
        $tempName = $resourceId.'_'.$date->format('Ymd_Hisu').'.pdf';
        $path = '/tmp/' . $tempName;

        // 法人検索API
        $rtnAPI = $acurisModel->individualsLookup($resourceId, $path);

        // ステータスコードが200以上300未満か判定
        if($rtnAPI->ok()){
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::DETAIL_FLG_ON);
            
            $rtnPath = $path;

        }else{
            // 検索履歴に追加
            $keywordModel->ins($companyId, $userId, self::DETAIL_FLG_ON, self::DETAIL_FLG_OFF);
        }

        return $rtnPath;
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
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
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
        foreach($excelData['result'] as $item){

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
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
    }

}