<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use TCPDF;


/**
 * 月別検索数
 */
class SearchReport extends Report
{
    use HasFactory;

    /**
     * PDF生成
     *
     * @param $fileName
     * @param $companyId
     * @param $pageNo
     * @param $dispType
     * @param $useMonth
     * @return string
     * @throws Exception
     */
    public function makePdf($fileName, $companyId, $pageNo, $dispType, $useMonth): string
    {
        // 表示データ取得
        $data = $this->getData($companyId, $pageNo, $dispType, $useMonth);

        $pdfData = [
            'date' => $data['date'],
            'companyName' => $data['companyName'],
            'monthSearchCount' => $data['monthSearchCount'],
            'yearSearchCount' => $data['yearSearchCount'],
            'webDepositInfo' => $data['webDepositInfo'],
            'apiDepositInfo' => $data['apiDepositInfo'],
            'depositList' => $data['depositList'],
            'detail' => $data['detail'],
            'dispType' => $dispType,
        ];

        //PDF生成
        $pdfTemplate = 'pdf.pdfSearchReport';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output( $fileName, "I" );
    }

    /**
     * ファイル名を取得
     * @param $companyId
     * @return string
     */
    public function getFileName($companyId): string
    {
        $fileName = '月別検索数-%s.pdf';
        return mb_convert_encoding(sprintf($fileName, $companyId), 'SJIS-WIN', 'UTF-8');
    }

}
