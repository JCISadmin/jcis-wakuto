<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;
use TCPDF;


/**
 * 検索
 */
class PdfSearchReport extends BaseModel
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
    public function makePdf($companyId, $fileName): string
    {


        $keywordModel = new TKeywordHistory();
        $userDetail = new MUserDetail();
        $userCompany = new MUserCompany();
        $data = [];
        $companyInfo = $userDetail->getByCompanyId($companyId);
        $companyName = $userCompany->getCompanyName($companyId);

        foreach($companyInfo as $userInfo){
            //月別検索数を取得
            $searchCountData = $keywordModel->getSearchCountByMonth($companyId, $userInfo->userId);

            foreach($searchCountData as $monthlySearchCountData){
                $data[$monthlySearchCountData->searchMonth]['month'] = $monthlySearchCountData->searchMonth;
                $data[$monthlySearchCountData->searchMonth]['userInfo'][$userInfo->userId] = [
                    'user' => $userInfo->name,
                    'count' => $monthlySearchCountData->MonthlySearchCount,
                ];

                //検索数を月ごとに合算
                if(array_key_exists('totalCount', $data[$monthlySearchCountData->searchMonth])){
                    $data[$monthlySearchCountData->searchMonth]['totalCount'] += $monthlySearchCountData->MonthlySearchCount;
                }else{
                    $data[$monthlySearchCountData->searchMonth]['totalCount'] = $monthlySearchCountData->MonthlySearchCount;
                }
            }
        }

        krsort($data);

        $pdfData = [
            'companyName' => $companyName,
            'detail' => $data,
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
