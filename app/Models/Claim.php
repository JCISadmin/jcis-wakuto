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

        $planModel = new TContractPlan();
        //WEB契約情報を取得
        $planInfo[self::PLAN_TYPE_WEB] = $planModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        //API契約情報を取得
        $planInfo[self::PLAN_TYPE_API] = $planModel->getPlan($companyId, self::PLAN_TYPE_API);

        foreach($data[0]->items as $key => $itemAry){
            //$key = web または api
            $workAry = $this->getItemInfo($key, $itemAry, $data[0]->adjustNote, $data[0]->adjustPrice);
            $detail = array_merge($detail + $workAry);
        }

        $workAry = $this->getItemInfo('adjust', [], $data[0]->adjustNote, $data[0]->adjustPrice);
        $detail = array_merge($detail + $workAry);

        $companyInfo = $this->getCompanyInfo();
        $pdfData['claimInfo'] = (array)$data[0];
        $pdfData['companyInfo'] = $companyInfo;
        $pdfData['detail'] = $detail;

        //PDF生成
        $pdfTemplate = 'pdf.pdfClaim';
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());

        $pdf = $this->setImage($pdf);

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

        //トライアル費用
        if($itemInfo['trial']['price'] > 0){
            $detail[$subjectTrial] = [
                    'trial' => [
                        'itemName' => self::ITEM_DEPOSIT,
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

        //デポ不足
        if($itemInfo['payPerUse']['price'] > 0){
            $detail[$subjectRegular]['payPerUse'] = [
                'itemName' => self::ITEM_SHORTAGE,
                'amount' => $itemInfo['payPerUse']['amount'].'件',
                'unitPrice' => $itemInfo['payPerUse']['unitPrice'],
                'price' => $itemInfo['payPerUse']['price'],
            ];
        }

        return $detail;

    }

    /**
     * PDFに画像を挿入
     * @param  $pdf
     * @return  object
     */
    public function setImage($pdf): object
    {
        // 社名画像
        $companyNameImage = config('hds.claim.imageFileName.companyName');

        if($companyNameImage !== ''){
            $pdf->Image(storage_path(self::IMAGE_PATH . '/' . $companyNameImage), 105, 15, 60, 15, 'JPEG');
        }

        // 会社印画像
        $companyStampImage = config('hds.claim.imageFileName.companyStamp');

        $pdf->Image(storage_path(self::IMAGE_PATH . '/' . $companyStampImage), 175, 25, 20, 20, 'JPEG');

        return $pdf;
    }
}
