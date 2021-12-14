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
        $userCompany = new MUserCompany();
        $data = [];
        $companyInfo = $userCompany->get($companyId);

        foreach($companyInfo['contractPlan'] as $contractItem){
            if(is_null($contractItem) === false){
                //契約情報ありの場合
                if(is_null($contractItem['userDetail']) === false){
                    //ユーザー情報ありの場合
                    foreach($contractItem['userDetail'] as $detailItem){
                        //月別検索数を取得
                        $searchCountData = $keywordModel->getSearchCountByMonth($companyId, $detailItem['userId']);

                        foreach($searchCountData as $monthlySearchCountData){
                            $data[$monthlySearchCountData->searchMonth]['month'] = $monthlySearchCountData->searchMonth;
                            $data[$monthlySearchCountData->searchMonth]['userInfo'][$detailItem['userId']] = [
                                'user' => $detailItem['name'],
                                'count' => $monthlySearchCountData->MonthlySearchCount,
                            ];
                        }
                    }
                }
            }
        }

        //月別検索総数をカウント
        foreach($data as $key => $item){
            $data[$key]['totalCount'] = 0;
            foreach($item['userInfo'] as $value){
                $totalCount = $value['count'];
                $data[$key]['totalCount'] += $totalCount;

            }
        }

        krsort($data);

        $pdfData = [
            'companyName' => $companyInfo['userCompany']['name'],
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
