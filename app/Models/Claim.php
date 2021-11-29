<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use TCPDF;

class Claim extends BaseModel
{
    use HasFactory;

    const IMAGE_PATH = 'app';

    /**
     * PDF生成
     *
     * @param $companyId
     * @param $claimMonth
     * @param $fileName
     * @param bool $isFile
     * @return string
     * @throws Exception
     */
    public function makePdf($companyId, $claimMonth, $fileName, bool $isFile = false): string
    {
        $model = new TClaim();
        $data = $model->getList($claimMonth, null, $companyId, null, false, false);
        $detail = [];

        foreach($data[0]->items as $key => $itemAry){
            //$key = web または api
            $workAry = $this->getItemInfo($key, $itemAry, $data[0]->adjustNote, $data[0]->adjustPrice);
            $detail = $detail + $workAry;
        }

        $workAry = $this->getItemInfo('adjust', [], $data[0]->adjustNote, $data[0]->adjustPrice);
        $detail = $detail + $workAry;

        $companyInfo = $this->getCompanyInfo();
        $pdfData['claimInfo'] = (array)$data[0];

        $pdfData['companyInfo'] = $companyInfo;
        $pdfData['detail'] = $detail;

        //PDF生成
        $pdfTemplate = 'pdf.pdfClaim';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',8);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        $pdf = $this->setImage($pdf, $pdfData);

        if($isFile== false){

            return $pdf->Output( $fileName, "S" );
        }else{

            if (!file_exists(storage_path('app/pdfClaim'))) {
                mkdir(storage_path('app/pdfClaim'));
            }

            $filePath = storage_path('app/pdfClaim/' . $fileName);

            $pdf->Output( $filePath, "F" );
            return $filePath;
        }
    }

    /**
     * ファイル名を取得
     * @param $claimMonth
     * @return string
     */
    public function getFileName($claimMonth): string
    {
        $fileName = '請求書-%s.pdf';
        return sprintf($fileName, $claimMonth);
    }

    /**
     * 会社情報を取得
     * @param
     * @return array $companyInfo
     */
    public function getCompanyInfo(): array
    {
        $query = DB::table('mCompany');
        $companyInfo = $query->first();
        return (array)$companyInfo;
    }

    /**
     * 請求書に表示する品目情報を取得
     * @param $type
     * @param $itemInfo
     * @param $adjustNote
     * @param $adjustPrice
     * @return array $detail
     */
    public function getItemInfo($type, $itemInfo, $adjustNote, $adjustPrice): array
    {
        $detail = [];

        switch($type){
            case 'web':
                $subjectTrial = config('hds.subject.web.trial');
                $subjectRegular = config('hds.subject.web.regular');
                break;

            case 'api':
                $subjectTrial = config('hds.subject.api.trial');
                $subjectRegular = config('hds.subject.api.regular');
                break;

            case 'adjust':
                $subject = $adjustNote;
                if ($adjustPrice !== 0) {
                    $detail[$subject]['adjust'] = [
                        'itemName' => $subject,
                        'amount' => null,
                        'unitPrice' => null,
                        'price' => $adjustPrice,
                    ];
                }
                return $detail;

            default:
                return $detail;
        }

        //トライアル費用（検索代 + ID代（無料））
        if($itemInfo['trial']['price'] > 0){
            $detail[$subjectTrial] = [
                    'trial' => [
                        'itemName' => self::ITEM_PAYPERUSE,
                        'amount' => $itemInfo['trial']['amount'].'件',
                        'unitPrice' => $itemInfo['trial']['unitPrice'],
                        'price' => $itemInfo['trial']['price'],
                    ],
                    'id' => [
                        'itemName' =>self::ITEM_TRIAL,
                        'amount' => '1か月',
                        'unitPrice' => 0,
                        'price' => 0,
                    ],
            ];
        }

        switch($itemInfo['contractPlanId']){
            case TClaim::TYPE_ALL_DEPOSIT:
                //ID代
                if($itemInfo['id']['price'] > 0){
                    $detail[$subjectRegular]['id'] = [
                        'itemName' => self::ITEM_ID,
                        'amount' => $itemInfo['id']['amount'].'か月',
                        'unitPrice' => $itemInfo['id']['unitPrice'],
                        'price' => $itemInfo['id']['price'],
                    ];
                }
        
                //デポジット代
                if($itemInfo['deposit']['price'] > 0){
                    $detail[$subjectRegular]['deposit']= [
                        'itemName' => self::ITEM_DEPOSIT,
                        'amount' => $itemInfo['deposit']['amount'].'件',
                        'unitPrice' => $itemInfo['deposit']['unitPrice'],
                        'price' => $itemInfo['deposit']['price'],
                    ];
                }
        
                
                //検索代
                if($itemInfo['payPerUse']['price'] > 0){
                    $detail[$subjectRegular]['payPerUse'] = [
                        'itemName' => self::ITEM_SHORTAGE,
                        'amount' => $itemInfo['payPerUse']['amount'].'件',
                        'unitPrice' => $itemInfo['payPerUse']['unitPrice'],
                        'price' => $itemInfo['payPerUse']['price'],
                    ];
                }
                break;

            case TClaim::TYPE_ID_DEPOSIT:
            case TClaim::TYPE_MONTHLY:
                //ID代
                if($itemInfo['id']['price'] > 0){
                    $detail[$subjectRegular]['id'] = [
                        'itemName' => self::ITEM_ID,
                        'amount' => $itemInfo['id']['amount'].'か月',
                        'unitPrice' => $itemInfo['id']['unitPrice'],
                        'price' => $itemInfo['id']['price'],
                    ];
                }
                
                //検索代
                if($itemInfo['payPerUse']['price'] > 0){
                    $detail[$subjectRegular]['payPerUse'] = [
                        'itemName' => self::ITEM_PAYPERUSE,
                        'amount' => $itemInfo['payPerUse']['amount'].'件',
                        'unitPrice' => $itemInfo['payPerUse']['unitPrice'],
                        'price' => $itemInfo['payPerUse']['price'],
                    ];
                }
                break;
        }

        return $detail;

    }

    /**
     * PDFに画像を挿入
     * @param  $pdf
     * @param  $pdfData
     * @return  object
     */
    public function setImage($pdf, $pdfData): object
    {    
       // 社名画像
        $nameRate = 0.015;
        $namefilePath = storage_path(self::IMAGE_PATH . '/' . config('hds.claim.imageFileName.companyName'));

        if($namefilePath !== ''){
            $nameSize = getimagesize($namefilePath);
            $nameWidth = $nameSize[0] * $nameRate;
            $nameHight = $nameSize[1] * $nameRate;
            $pdf->Image($namefilePath, 110, 20, $nameWidth, $nameHight, 'JPEG');
        }

        // 会社印影画像
        $stampRate = 0.07;
        $stampFilePath = storage_path(self::IMAGE_PATH . '/' . config('hds.claim.imageFileName.companyStamp'));

        if($namefilePath !== ''){
            $stampSize = getimagesize($stampFilePath);
            $stampWidth = $stampSize[0] * $stampRate;
            $stampHight = $stampSize[1] * $stampRate;
            $pdf->Image($stampFilePath, 170, 32, $stampWidth, $stampHight, 'JPEG');
        }

        //会社情報
        $x = 32;//出力開始位置
        $pdf->Text( 110, $x, $pdfData['companyInfo']['name']);
        if(is_null($pdfData['claimInfo']['chargeName']) === false){
            $pdf->Text( 110, $x = $x + 4, '担当：'.$pdfData['claimInfo']['chargeName']);
            $pdf->Text( 110, $x = $x + 28, is_null($pdfData['claimInfo']['chargeMail']) ? '' : $pdfData['claimInfo']['chargeMail']);
            $x = $x - 28;//$xを窓口担当者名の出力位置に戻す
        }

        $pdf->Text( 110, $x = $x + 2, '');
        $pdf->Text( 110, $x = $x + 4, '〒'.substr_replace($pdfData['companyInfo']['postCode'], '-', 3, 0));
        $pdf->MultiCell(70, 8, $pdfData['companyInfo']['address'], 0, 'L', false, 0, 110, $x = $x + 4);
        $pdf->Text( 110, $x = $x + 10, 'TEL：'.$pdfData['companyInfo']['tel']);
        $pdf->Text( 110, $x = $x + 4, 'FAX：'.$pdfData['companyInfo']['fax']);

        return $pdf;
    }
}
