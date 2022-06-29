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
        $claimModel = new Claim();
        $tClaimModel = new TClaim;
        $tClaimDetailModel = new TClaimDetail;

        $data = $tClaimModel->getList($claimMonth, null, $companyId, null, false, false);
        $companyInfo = $this->getCompanyInfo();
        
        //tClaimDetailテーブルから費目情報(補正額以外)を取得
        $expenseList = $tClaimDetailModel->getExpenseList($companyId[0], $claimMonth);
        //DBから取得できない場合、費目情報を計算して取得
        if($expenseList === []){
            $expenseList = $claimModel->getExpenseList($companyId, $claimMonth);
        }    
        //tClaimDetailテーブルから費目情報(補正額)を取得
        $expenseAdjustList = $tClaimDetailModel->getExpenseAdjustList($companyId[0], $claimMonth);

        $pdfData['claimInfo'] = (array)$data[0];
        $pdfData['companyInfo'] = $companyInfo;
        $pdfData['expenseList'] = $expenseList;
        $pdfData['expenseAdjustList'] = $expenseAdjustList;

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
     * 請求費目一覧(補正額除く)を計算取得
     * @param $companyId
     * @param $claimMonth
     * @return array $detail
     */
    public function getExpenseList($companyId, $claimMonth): array
    {
        $tClaimModel = new TClaim();

        $data = $tClaimModel->getList($claimMonth, null, $companyId, null, false, false);
        $detail = [];

        foreach($data[0]->items as $key => $itemAry){
            //$key = web または api

            $isAllDepo = false;
            $isIdDepo = false;
            $keyName = $key.'ContractTypeId';
            $contractTypeId = $data[0]->$keyName;
            if($contractTypeId === TClaim::TYPE_ALL_DEPOSIT){
                //全額デポジットプランの場合
                $isAllDepo = true;
            }
            if($contractTypeId === TClaim::TYPE_ID_DEPOSIT){
                //ID代のみデポジットプランの場合
                $isIdDepo = true;
            }

            $workAry = $this->getExpenseItem($key, $itemAry, $isAllDepo, $isIdDepo);
            $detail = array_merge($detail,$workAry);
        }

        return $detail;
    }

    /**
     * 請求書費目(WEB・API別)を計算取得
     * @param $type
     * @param $itemInfo
     * @param bool $isAllDepo
     * @return array $detail
     */
    public function getExpenseItem($type, $itemInfo, $isAllDepo = false, $isIdDepo = false): array
    {
        $detail = [];

        switch($type){
            case 'web':
                //利用システムを設定
                $subjectTrial = config('hds.subject.web.trial');
                $subjectRegular = config('hds.subject.web.regular');
                break;

            case 'api':
                //利用システムを設定
                $subjectTrial = config('hds.subject.api.trial');
                $subjectRegular = config('hds.subject.api.regular');
                break;

            default:
                return $detail;
        }

        //トライアル費用（検索代 + ID代（無料））
        if($itemInfo['trial']['price'] > 0){

            //トライアル料金が発生する場合、タイトルを追加
            $detail[] = [
                'type' => 'title',
                'useFlg' => 1,
                'itemName' => $subjectTrial,
                'amount' => 0,
                'unitPrice' => 0,
                'price' => 0,
            ];
    
            //トライアル料金(検索代・ID代)
            $detail[] = [
                [
                    'type' => 'id',
                    'useFlg' => 1,    
                    'itemName' => '1 . '.self::ITEM_TRIAL,
                    'amount' => 1,
                    'unitPrice' => 0,
                    'price' => 0,
                ],
                [
                    'type' => 'search',
                    'useFlg' => 1,    
                    'itemName' => '2 . '.self::ITEM_PAYPERUSE,
                    'amount' => $itemInfo['trial']['amount'],
                    'unitPrice' => $itemInfo['trial']['unitPrice'],
                    'price' => $itemInfo['trial']['price'],
                ],
            ];
        }

        if($itemInfo['id']['price'] > 0 || $itemInfo['deposit']['price'] > 0 || $itemInfo['payPerUse']['price'] > 0){
            //本契約料金が発生する場合、タイトルを追加
            $detail[] = [
                'type' => 'title',
                'useFlg' => 1,
                'itemName' => $subjectRegular,
                'amount' => 0,
                'unitPrice' => 0,
                'price' => 0,
            ];
        }

        //費目名採番
        $prefix = 1;

        //ID代
        if($isAllDepo || $isIdDepo){
            $idItemName = self::ITEM_ID_YEAR;
        }else{
            $idItemName = self::ITEM_ID_MONTH;
        }

        if($itemInfo['id']['price'] > 0){
            $detail[] = [
                'type' => 'id',
                'useFlg' => 1,
                'itemName' => $prefix.' . '.$idItemName,
                'amount' => $itemInfo['id']['amount'],
                'unitPrice' => $itemInfo['id']['unitPrice'],
                'price' => $itemInfo['id']['price'],
            ];
            $prefix += 1;
        }

        //デポジット代
        if($itemInfo['deposit']['price'] > 0){
            $detail[] = [
                'type' => 'search',
                'useFlg' => 1,
                'itemName' => $prefix.' . '.self::ITEM_DEPOSIT,
                'amount' => $itemInfo['deposit']['amount'],
                'unitPrice' => $itemInfo['deposit']['unitPrice'],
                'price' => $itemInfo['deposit']['price'],
            ];
            $prefix += 1;
        }

        //検索代
        if($isAllDepo){
            $payPerUseName = self::ITEM_SHORTAGE;
        }else{
            $payPerUseName = self::ITEM_PAYPERUSE;
        }
        if($itemInfo['payPerUse']['price'] > 0){
            $detail[] = [
                'type' => 'search',
                'useFlg' => 1,
                'itemName' => $prefix.' . '.$payPerUseName,
                'amount' => $itemInfo['payPerUse']['amount'],
                'unitPrice' => $itemInfo['payPerUse']['unitPrice'],
                'price' => $itemInfo['payPerUse']['price'],
            ];
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
