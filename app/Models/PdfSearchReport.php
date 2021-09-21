<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Datetime;
use TCPDF;
use App\Models\TKeywordHistory;
use App\Models\MUserCompany;


/**
 * 検索
 */
class PdfSearchReport extends BaseModel
{
    use HasFactory;

    const DATE_HIGH_VALUE = '3000-01-01';


    /**
     * PDF生成
     *
     * @param $companyId
     * @param $claimMonth
     * @param $fileName
     * @return string
     * @throws Exception
     */
    public function makePdf($companyId, $fileName): string
    {


        $keywordModel = new TKeywordHistory();
        $userCompany = new MUserCompany();
        $data = null;        
        $companyInfo = $userCompany->get($companyId);

        $printMonth = date_format(new DateTime(), 'Y-m');
        //$useStartYear = $useStartDate;

        foreach($companyInfo['contractPlan'] as $contractItem){
            if(is_null($contractItem) === false){
                //契約情報ありの場合

                //利用開始月
                $useStartDate = self::DATE_HIGH_VALUE;
                $useStartDate = $contractItem['useStartDate'];
                if (is_null($contractItem['useUpdateDate']) === false) {
                    // 利用更新日が指定されている場合、利用更新日基準とする
                    $useStartDate = $contractItem['useUpdateDate'];
                }
                $useStartMonth = date_format(new DateTime($useStartDate), 'Y-m');


                if(is_null($contractItem['userDetail']) === false){
                    //ユーザー情報ありの場合
                    
                    foreach($contractItem['userDetail'] as $detailItem){

                        //PDF発行月から利用開始月まで遡って月間検索数を取得
                        for($workMonth =  $printMonth; $workMonth >= $useStartMonth; ){
                            $year = date_format(new DateTime($workMonth), 'Y');
                            $month = date_format(new DateTime($workMonth), 'm');
                            $count = $keywordModel->getMonthSearchCount($companyId, $detailItem['userId'], null, $year, $month);
                            $data[$workMonth]['month'] = $workMonth;
                            $data[$workMonth]['userInfo'][$detailItem['userId']] = [
                                'user' => $detailItem['name'],
                                'count' => $count,
                            ];
                            $workMonth = (new Datetime($workMonth))->modify('-1 month')->format('Y-m');
                        }
                    }
                }
            }
        }

        //月別検索総数をカウント
        if(is_null($data) === false){
            foreach($data as $key => $item){
                $data[$key]['totalCount'] = 0;
                foreach($item['userInfo'] as $value){
                    $totalCount = $value['count'];
                    $data[$key]['totalCount'] += $totalCount;

                }
            }
        }

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