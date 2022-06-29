<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;
use TCPDF;


/**
 * 検索
 */
class PdfSearchReport extends Report
{
    use HasFactory;

    /**
     * PDF生成
     *
     * @param $companyId
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function makePdf($companyId, $fileName, $dispType, $useMonth): string
    {
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);
        if($dispType === 'all'){
            $detail = $this->getReportData($companyId);
        }elseif($dispType === 'month'){
            $detail = $this->getReportDatabyMonth($companyId, $useMonth);
        }

        $pdfData = [
            'companyName' => $companyName,
            'detail' => $detail,
        ];

        //PDF生成
        $pdfTemplate = 'pdf.pdfSearchReport';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        return $pdf->Output( $fileName, "S" );

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
