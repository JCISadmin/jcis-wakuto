<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use TCPDF;
use DateTime;

class Claim extends BaseModel
{
    use HasFactory;

    const IMAGE_PATH = 'app';

    const TYPE_ALL_DEPOSIT = 'allDepo';
    const TYPE_ID_DEPOSIT = 'idDepo';
    const TYPE_MONTHLY = 'allMonth';

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

            return $pdf->Output( $fileName, "I" );
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

            $contractTypeId = $itemAry['contractType'];
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
    private function getExpenseItem($type, $itemInfo, $isAllDepo = false, $isIdDepo = false): array
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
                'type' => 'id',
                'useFlg' => 1,    
                    'useFlg' => 1,    
                'useFlg' => 1,    
                    'useFlg' => 1,    
                'useFlg' => 1,    
                'itemName' => '1 . '.self::ITEM_TRIAL,
                'amount' => 1,
                'unitPrice' => 0,
                'price' => 0,
            ];
            $detail[] = [
                    'type' => 'search',
                    'useFlg' => 1,    
                    'itemName' => '2 . '.self::ITEM_PAYPERUSE,
                    'amount' => $itemInfo['trial']['amount'],
                    'unitPrice' => $itemInfo['trial']['unitPrice'],
                    'price' => $itemInfo['trial']['price'],
            ];
        }

        if($itemInfo['id']['price'] > 0 || $itemInfo['deposit']['price'] > 0 || $itemInfo['payPerUse']['total'] > 0){
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
        if($itemInfo['payPerUse']['total'] > 0){
            unset($itemInfo['payPerUse']['total']);
            foreach($itemInfo['payPerUse'] as $payPerUseItem){
                //検索数0件は除外
                if($payPerUseItem['amount'] === 0){
                    continue;
                }
                $detail[] = [
                    'type' => 'search',
                    'useFlg' => 1,
                    'itemName' => $prefix.' . '.$payPerUseName,
                    'amount' => $payPerUseItem['amount'],
                    'unitPrice' => $payPerUseItem['unitPrice'],
                    'price' => $payPerUseItem['price'],
                ];

                $prefix += 1;
            }
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

    /**
     * 請求検索詳細取得
     * @param  $pdf
     * @param  $pdfData
     * @return  
     */
    public function getSearchDetail($companyId, $claimMonth): array|null
    {
        $keywordModel = new TKeywordHistory();
        $mUserDetailModel = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();
        
        $fromMonth = new DateTime($claimMonth);
        $startDate = $fromMonth->format('Y-m-1');
        $endDate = $fromMonth->format('Y-m-t');

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);

        //ID数
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);
        
        $data = [];
        
        $data['searchList'] = [];
        $data[self::PLAN_TYPE_WEB]['totalSearchCount'] = 0;
        $data[self::PLAN_TYPE_API]['totalSearchCount'] = 0;
        $data[self::PLAN_TYPE_WEB]['totalSearchPrice'] = 0;
        $data[self::PLAN_TYPE_API]['totalSearchPrice'] = 0;

        $data['contractInfo'] = $contractPlanDetailModel->getDetailByMonth($companyId, $startDate, $endDate);

        //トライアル検索情報を取得
        $trialSearchData = $this->getTrialSearchData($companyId, $userIds, $webPlanInfo, $apiPlanInfo, $startDate, $endDate);
        if($trialSearchData !== []){
            $data['searchList'][] = $trialSearchData;
        }

        //プラン別ループ(tContractPlanDetail)
        foreach($data['contractInfo'] as $contractItem){
            //適用開始日/終了日が月初/月末を超過する場合 日付調整
            if($startDate > $contractItem->contractStartDate){
                $contractStartDate = $startDate;
            }else{
                $contractStartDate = $contractItem->contractStartDate;
            }
            if($endDate < $contractItem->contractEndDate){
                $contractEndDate = $endDate;
            }else{
                $contractEndDate = $contractItem->contractEndDate;
            }
                                    
            //検索数情報
            $searchList = $keywordModel->getSearchCountByReport($companyId, $userIds[$contractItem->planType], $contractItem->planType, $contractStartDate, $contractEndDate);
            $wkAry = [];

            foreach($searchList as $searchItem){

                $depositName = '';
                //全額デポジット かつ chargeFlg=0 は検索料金無し
                if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === self::TYPE_ALL_DEPOSIT){
                    $unitPrice = 0;
                    $price = 0;
                }else{
                    $unitPrice = $contractItem->searchUnitPrice;
                    $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                }
                    
                $wkAry[] = [
                    'user' => $searchItem['userId'].' / '.$searchItem['name'].$depositName,
                    'unitPrice' => $unitPrice,
                    'count' => $searchItem['searchCount'],
                    'price' => $price,
                    'contractStartDate' => $contractStartDate,
                    'contractEndDate' => $contractEndDate,
                    'chargeFlg' => $searchItem['chargeFlg'],
                    'planType' => $contractItem->planType,
                ];
                
                //月毎検索数/金額
                $data[$contractItem->planType]['totalSearchCount'] += $searchItem['searchCount'];
                $data[$contractItem->planType]['totalSearchPrice'] += $price;
                
            }

            $data['searchList'] = array_merge($data['searchList'], $wkAry);
        }

        return $data;
    }


    /**
     * トライアル検索詳細取得
     * @param  $companyId
     * @param  $userIds
     * @param  $webPlanInfo
     * @param  $apiPlanInfo
     * @param  $startDate
     * @param  $endDate
     * @return array
     */
    public function getTrialSearchData($companyId, $userIds, $webPlanInfo, $apiPlanInfo, $startDate, $endDate): array
    {
        $keywordModel = new TKeywordHistory();
        $data = [];

        //WEB
        $webEndTrial = date("Y-m-d",strtotime($webPlanInfo['useStartDate']."-1 day"));
        $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webPlanInfo['startTrial'], $webEndTrial, true);
        //トライアル期間の検索がある場合
        if(!is_null($webTrialSearchList)){

            //請求期間内にトライアル期間が含まれる場合のみ
            if( $webPlanInfo['startTrial'] < $endDate && $webEndTrial > $startDate){

                foreach($webTrialSearchList as $searchItem){

                    $unitPrice = $webPlanInfo['trialSearchUnitPrice'];;
                    $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];

                    $data[] = [
                        'user' => $searchItem['userId'].' / '.$searchItem['name'].' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $webPlanInfo['startTrial'],
                        'contractEndDate' => $webEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'planType' => self::PLAN_TYPE_WEB,
                    ];

                }
            }
        }
        
        //API
        $apiEndTrial = date("Y-m-d",strtotime($apiPlanInfo['useStartDate']."-1 day"));
        $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiPlanInfo['startTrial'], $apiEndTrial, true);
        //トライアル期間の検索がある場合
        if(!is_null($apiTrialSearchList)){

            //請求期間内にトライアル期間が含まれる場合のみ
            if( $apiPlanInfo['startTrial'] < $endDate && $apiEndTrial > $startDate){
                
                foreach($apiTrialSearchList as $searchItem){
                        
                    $unitPrice = $apiPlanInfo['trialSearchUnitPrice'];
                    $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];

                    $data[] = [
                        'user' => $searchItem['userId'].' / '.$searchItem['name'].' (トライアル)',
                        'unitPrice' => $unitPrice,
                        'count' => $searchItem['searchCount'],
                        'price' => $price,
                        'contractStartDate' => $apiPlanInfo['startTrial'],
                        'contractEndDate' => $apiEndTrial,
                        'chargeFlg' => $searchItem['chargeFlg'],
                        'planType' => self::PLAN_TYPE_API,
                    ];
                }
            }
        }

        return $data;
    }

}
