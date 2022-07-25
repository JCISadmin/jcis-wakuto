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
    public function makePdf($fileName, $companyId, $dispType, $month, $pageNo): string
    {
        //現在日時
        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        //会社名
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($companyId);

        //表示データ取得
        $model = new Report();

        //全件指定
        if($dispType === 'all'){

            $pageInfo = $model->getReportPageInfo($companyId,$pageNo);
            $pageData = $pageInfo['pageData'];
            $pageAry = $pageData->items();
            $pageItem = array_values($pageAry);
            $year = $pageItem[0];
    
            $data = $model->getReportData($companyId, $year);

            $detail = [
                'month' => $data['month'],
                'year' => $data['year'],
            ];

        //月別指定
        }elseif($dispType === 'month'){

            $useMonth = new DateTime($month);
            //指定月が現在より先
            if($now < $useMonth){
                $detail = null;
            }else{
                $useY = $useMonth->format('Y');
                $useYM = $useMonth->format('Y-m');

                $data = $model->getReportData($companyId, $useY);

                if(!isset($data['month'][$useY][$useYM])){
                    $detail = null;
                }else{

                    $detail['month'][$useY][$useYM] = $data['month'][$useY][$useYM];
                    $detail['year'][$useY] = $data['year'][$useY];
                }
            }
        }

        //今月検索件数/年間検索件数/デポジット検索欄
        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $nowData = $model->getReportData($companyId, $now->format('Y'));

        if(isset($nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
            $monthSearchCount = $nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
        }
        if(isset($nowData['year'][$now->format('Y')]['totalSearchCount'])){
            $yearSearchCount = $nowData['year'][$now->format('Y')]['totalSearchCount'];
        }

        $pdfData = [
            'date' => $date,
            'companyName' => $companyName,
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'detail' => $detail,
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
