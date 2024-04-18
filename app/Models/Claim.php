<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Date;
use TCPDF;
use DateTime;

/**
 * 請求
 */
class Claim extends BaseModel
{
    use HasFactory;

    const IMAGE_PATH = 'app';

    //費目名採番
    private $prefix = 1;

    /**
     * 請求書PDF生成
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
        $tClaimModel = new TClaim;
        $tClaimDetailModel = new TClaimDetail;

        $data = $tClaimModel->getList($claimMonth, null, $companyId, null, false, false);
        $mCompanyModel = new MCompany();
        $companyInfo = $mCompanyModel->getCompanyInfo();

        //tClaimDetailテーブルから費目情報(補正額以外)を取得
        $expenseList = $tClaimDetailModel->getExpenseList($companyId[0], $claimMonth);
        //DBから取得できない場合、費目情報を計算して取得
        if($expenseList === []){
            $expenseList = $this->calcExpenseList($companyId, $claimMonth);
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

        $pdf->setPage(1);
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
     * 請求費目一覧(補正額除く)を計算取得
     * @param $companyId
     * @param $claimMonth
     * @return array $detail
     */
    public function calcExpenseList($companyIds, $claimMonth): array
    {
        $tClaimModel = new TClaim();

        $data = $tClaimModel->getList($claimMonth, null, $companyIds, null, false, false);
        $detail = [];

        $dtClaimMonth = new DateTime($claimMonth . '-01');
        $claimTerm = [
            'startDate' => $dtClaimMonth->format('Y/m/d'),
            'endDate' => $dtClaimMonth->format('Y/m/t')
        ];

        $wkStartTermDate = '1900-01-01';
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

            $itemAry['claimTerm'] = $claimTerm;
            $itemAry['webContractInfo'] = [];
            $itemAry['apiContractInfo'] = [];

            if (isset($data[0]->webContractInfo[0])) {
                $itemAry['webContractInfo'] = $data[0]->webContractInfo[0];
                $wkStartTermDate = $itemAry['webContractInfo']['contractStartDate'];
            }
            if (isset($data[0]->apiContractInfo[0])) {
                $itemAry['apiContractInfo'] = $data[0]->apiContractInfo[0];
                if ($wkStartTermDate < $itemAry['apiContractInfo']['contractStartDate']) {
                    $wkStartTermDate = $itemAry['apiContractInfo']['contractStartDate'];
                }
            }

            $workAry = $this->getExpenseItem($companyIds, $key, $itemAry, $isAllDepo);
            $detail = array_merge($detail,$workAry);
        }

        // 海外検索
        $wkStartTermDate = $this->formatDateInvoice($wkStartTermDate, 'Y/m/d');
        if ($wkStartTermDate > $claimTerm['startDate']) {
            $claimTerm['startDate'] = $wkStartTermDate;
        }

        $workAry = $this->getAddExpenseItem(SELF::PLAN_TYPE_ACURIS, $data[0]->acurisItems, $claimTerm);
        $detail = array_merge($detail,$workAry);

        return $detail;
    }

    /**
     * 請求書費目(WEB・API別)を計算取得
     * @param $type
     * @param $itemInfo
     * @param bool $isAllDepo
     * @return array
     */
    private function getExpenseItem($companyIds, $type, $itemInfo, $isAllDepo = false): array
    {
        $mUserDetail = new MUserDetail();

        $detail = [];

        switch($type){
            case self::PLAN_TYPE_WEB:
                //利用システムを設定
                $subjectTrial = config('hds.subject.web.trial');
                $subjectRegular = config('hds.subject.web.regular');

                break;

            case self::PLAN_TYPE_API:
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
                'unit' => '',
                'unitPrice' => 0,
                'price' => 0,
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
            ];

            //トライアル料金(検索代・ID代)
            $detail[] = [
                'type' => 'id',
                'useFlg' => 1,
                'itemName' => sprintf('[利用期間 %s] ', $itemInfo['trial']['startDate'] . '-' . $itemInfo['trial']['endDate']) . self::ITEM_TRIAL,
                'amount' => $mUserDetail->getUserCount($companyIds[0], $type),
                'unit' => 'ID',
                'unitPrice' => 0,
                'price' => 0,
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
            ];

            $detail[] = [
                'type' => 'search',
                'useFlg' => 1,
                'itemName' => sprintf('[利用期間 %s] ', $itemInfo['trial']['startDate'] . '-' . $itemInfo['trial']['endDate']) . self::ITEM_PAYPERUSE,
                'amount' => $itemInfo['trial']['amount'],
                'unit' => '件',
                'unitPrice' => $itemInfo['trial']['unitPrice'],
                'price' => $itemInfo['trial']['price'],
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
            ];

        }

        if($itemInfo['idTotalPrice'] > 0 || $itemInfo['deposit']['price'] > 0 || $itemInfo['payPerUse']['total'] > 0){
            //本契約料金が発生する場合、タイトルを追加
            $detail[] = [
                'type' => 'title',
                'useFlg' => 1,
                'itemName' => $subjectRegular,
                'amount' => 0,
                'unit' => '',
                'unitPrice' => 0,
                'price' => 0,
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
            ];
        }

        //ID代
        if($itemInfo['idTotalPrice'] > 0){
            // 月額ID
            if ($itemInfo['idMonthly']['price'] > 0) {

                if ($type == 'web') {
                    $workStartDate = $this->formatDateInvoice($itemInfo['webContractInfo']['contractStartDate'], 'Y/m/d');
                } else {
                    $workStartDate = $this->formatDateInvoice($itemInfo['apiContractInfo']['contractStartDate'], 'Y/m/d');
                }

                if ($itemInfo['claimTerm']['startDate'] < $workStartDate) {
                    $itemInfo['claimTerm']['startDate'] = $workStartDate;
                }

                $termDate = $itemInfo['claimTerm']['startDate'] . '-' . $itemInfo['claimTerm']['endDate'];

                $detail[] = [
                    'type' => 'id',
                    'useFlg' => 1,
                    'itemName' => sprintf('[利用期間 %s] ', $termDate) . self::ITEM_ID_MONTH,
                    'amount' => $itemInfo['idMonthly']['amount'],
                    'unit' => 'ID',
                    'unitPrice' => $itemInfo['idMonthly']['unitPrice'],
                    'price' => $itemInfo['idMonthly']['price'],
                    'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
                ];

            }

            // 年額ID
            if ($itemInfo['idYearly']['price'] > 0) {
                $idYearlyTerm = $this->formatDateInvoice($itemInfo['idYearly']['startDate'], 'Y/m/d') . '-' . $this->formatDateInvoice($itemInfo['idYearly']['endDate'], 'Y/m/d');
                $detail[] = [
                    'type' => 'id',
                    'useFlg' => 1,
                    'itemName' => sprintf('[利用期間 %s] ', $idYearlyTerm) . self::ITEM_ID_YEAR,
                    'amount' => $itemInfo['idYearly']['amount'],
                    'unit' => 'ID',
                    'unitPrice' => $itemInfo['idYearly']['unitPrice'],
                    'price' => $itemInfo['idYearly']['price'],
                    'paymentPlan' => $type.TClaim::PAYMENT_PLAN_YEAR,
                ];

            }
        }

        //デポジット代
        if($itemInfo['deposit']['price'] > 0){
            $depositTerm = $this->formatDateInvoice($itemInfo['deposit']['startDate'], 'Y/m/d') . '-' . $this->formatDateInvoice($itemInfo['deposit']['endDate'], 'Y/m/d');
            $detail[] = [
                'type' => 'search',
                'useFlg' => 1,
                'itemName' => sprintf('[利用期間 %s] ', $depositTerm) . self::ITEM_DEPOSIT,
                'amount' => $itemInfo['deposit']['amount'],
                'unit' => '件',
                'unitPrice' => $itemInfo['deposit']['unitPrice'],
                'price' => $itemInfo['deposit']['price'],
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_YEAR,
            ];
            $this->prefix += 1;
        }

        //検索代
        if($itemInfo['payPerUse']['total'] > 0){
            unset($itemInfo['payPerUse']['total']);
            foreach($itemInfo['payPerUse'] as $payPerUseItem){
                //検索数0件は除外
                if($payPerUseItem['amount'] === 0){
                    continue;
                }

                $claimTerm = '';
                if (isset($payPerUseItem['startDate'])) {
                    $claimTerm = $this->formatDateInvoice($payPerUseItem['startDate'], 'Y/m/d');
                }
                if (isset($payPerUseItem['endDate'])) {
                    $claimTerm .= '-' . $this->formatDateInvoice($payPerUseItem['endDate'], 'Y/m/d');
                }

                $detail[] = [
                    'type' => 'search',
                    'useFlg' => 1,
                    'itemName' => sprintf('[利用期間 %s] ', $claimTerm) . $payPerUseItem['title'],
                    'amount' => $payPerUseItem['amount'],
                    'unit' => '件',
                    'unitPrice' => $payPerUseItem['unitPrice'],
                    'price' => $payPerUseItem['price'],
                    'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
                ];

            }
        }
        return $detail;
    }

    /**
     * 請求書費目(WEB/API以外)を計算取得
     *
     * @param $type
     * @param $itemInfo
     * @param $claimTerm
     * @return array
     */
    private function getAddExpenseItem($type, $itemInfo, $claimTerm): array
    {
        $detail = [];

        switch($type){
            case self::PLAN_TYPE_ACURIS:
                //利用システムを設定
                $subject = config('hds.subject.acuris.regular');
                break;

            default:
                return $detail;
        }

        // 検索代
        if($itemInfo['payPerUse']['total'] > 0){
            //料金が発生する場合、タイトルを追加
            $detail[] = [
                'type' => 'title',
                'useFlg' => 1,
                'itemName' => $subject,
                'amount' => 0,
                'unit' => '',
                'unitPrice' => 0,
                'price' => 0,
                'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
            ];

            unset($itemInfo['payPerUse']['total']);
            foreach($itemInfo['payPerUse'] as $payPerUseItem){
                //検索数0件は除外
                if($payPerUseItem['amount'] === 0){
                    continue;
                }
                // 一覧検索/詳細検索の文言を変更
                if($payPerUseItem['detailFlg'] === SELF::DETAIL_FLG_OFF){
                    $payPerUseName = self::ITEM_ACURIS;
                }else{
                    $payPerUseName = self::ITEM_ACURIS_DETAIL;
                }

                $detail[] = [
                    'type' => 'search',
                    'useFlg' => 1,
                    'itemName' => sprintf('[利用期間 %s] ', $claimTerm['startDate'] . '-' . $claimTerm['endDate']) . $payPerUseName,
                    'amount' => $payPerUseItem['amount'],
                    'unit' => '件',
                    'unitPrice' => $payPerUseItem['unitPrice'],
                    'price' => $payPerUseItem['price'],
                    'paymentPlan' => $type.TClaim::PAYMENT_PLAN_MONTH,
                ];
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
     * @param  $companyId
     * @param  $claimMonth
     * @return array
     */
    public function getSearchDetail($companyId, $claimMonth): array
    {
        $keywordModel = new TKeywordHistory();
        $mUserDetailModel = new MUserDetail();
        $contractPlanModel = new TContractPlan();
        $contractPlanDetailModel = new TContractPlanDetail();

        $fromMonth = new DateTime($claimMonth);
        $startDate = $fromMonth->format('Y-m-01');
        $endDate = $fromMonth->format('Y-m-t');

        $webPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_WEB);
        $apiPlanInfo = $contractPlanModel->getPlan($companyId, self::PLAN_TYPE_API);

        //ID配列
        $userIds[self::PLAN_TYPE_WEB] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_WEB);
        $userIds[self::PLAN_TYPE_API] = $mUserDetailModel->getList($companyId, self::PLAN_TYPE_API);

        $data = [];

        $data[self::PLAN_TYPE_WEB]['searchList'] = [];
        $data[self::PLAN_TYPE_API]['searchList'] = [];
        $data[self::PLAN_TYPE_WEB]['totalSearchCount'] = 0;
        $data[self::PLAN_TYPE_API]['totalSearchCount'] = 0;
        $data[self::PLAN_TYPE_WEB]['totalSearchPrice'] = 0;
        $data[self::PLAN_TYPE_API]['totalSearchPrice'] = 0;

        $contractInfo = $contractPlanDetailModel->getDetailByMonth($companyId, $startDate, $endDate);

        //トライアル検索情報を取得
        $trialSearchData = $this->getTrialSearchData($companyId, $userIds, $webPlanInfo, $apiPlanInfo, $startDate, $endDate);
        if($trialSearchData !== []){
            foreach($trialSearchData as $searchItem){
                // トライアル検索情報
                $data[$searchItem['planType']]['searchList'][] = $searchItem;

                //月毎検索数/金額
                $data[$searchItem['planType']]['totalSearchCount'] += $searchItem['count'];
                $data[$searchItem['planType']]['totalSearchPrice'] += $searchItem['price'];
            }
        }

        //プラン別ループ(tContractPlanDetail)
        foreach($contractInfo as $contractItem){
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

            foreach($searchList as $searchItem){

                $depositName = '';
                //全額デポジット かつ chargeFlg=0 は検索料金無し
                if($searchItem['chargeFlg'] === 0 && $contractItem->contractTypeId === TClaim::TYPE_ALL_DEPOSIT){
                    $unitPrice = 0;
                    $price = 0;
                }else{
                    $unitPrice = $contractItem->searchUnitPrice;
                    $price = $contractItem->searchUnitPrice * $searchItem['searchCount'];
                }

                $data[$contractItem->planType]['searchList'][] = [
                    'userId' => $searchItem['userId'],
                    'userName' => $searchItem['name'].$depositName,
                    'unitPrice' => $unitPrice,
                    'count' => $searchItem['searchCount'],
                    'price' => $price,
                    'contractStartDate' => $contractStartDate,
                    'contractEndDate' => $contractEndDate,
                    'chargeFlg' => $searchItem['chargeFlg'],
                ];

                //月毎検索数/金額
                $data[$contractItem->planType]['totalSearchCount'] += $searchItem['searchCount'];
                $data[$contractItem->planType]['totalSearchPrice'] += $price;
            }

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
        if(!is_null($webPlanInfo)){

            if(!is_null($webPlanInfo['useStartDate'])){
                $webPlanInfo['endTrial'] = date("Y-m-d",strtotime($webPlanInfo['useStartDate']."-1 day"));
            } else {
                $webPlanInfo['endTrial'] = date("Y-m-d");
            }

            //トライアル開始日/終了日が月初/月末を超過する場合 日付調整
            if($startDate > $webPlanInfo['startTrial']){
                $webPlanInfo['startTrial'] = $startDate;
            }
            if($endDate < $webPlanInfo['endTrial']){
                $webPlanInfo['endTrial'] = $endDate;
            }

            $webTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_WEB], self::PLAN_TYPE_WEB, $webPlanInfo['startTrial'], $webPlanInfo['endTrial'], true);
            //トライアル期間の検索がある場合
            if(!is_null($webTrialSearchList)){

                //請求期間内にトライアル期間が含まれる場合のみ
                if( $webPlanInfo['startTrial'] <= $endDate && $webPlanInfo['endTrial'] >= $startDate){

                    foreach($webTrialSearchList as $searchItem){

                        $unitPrice = $webPlanInfo['trialSearchUnitPrice'];;
                        $price = $webPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];

                        $data[] = [
                            'userId' => $searchItem['userId'],
                            'userName' => $searchItem['name'].' (トライアル)',
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem['searchCount'],
                            'price' => $price,
                            'contractStartDate' => $webPlanInfo['startTrial'],
                            'contractEndDate' => $webPlanInfo['endTrial'],
                            'chargeFlg' => $searchItem['chargeFlg'],
                            'planType' => self::PLAN_TYPE_WEB,
                        ];

                    }
                }
            }
        }

        //API
        if(!is_null($apiPlanInfo)){

            if(!is_null($apiPlanInfo['useStartDate'])){
                $apiPlanInfo['endTrial'] = date("Y-m-d",strtotime($apiPlanInfo['useStartDate']."-1 day"));
            } else {
                $apiPlanInfo['endTrial'] = date("Y-m-d");
            }

            //トライアル開始日/終了日が月初/月末を超過する場合 日付調整
            if($startDate > $apiPlanInfo['startTrial']){
                $apiPlanInfo['startTrial'] = $startDate;
            }
            if($endDate < $apiPlanInfo['endTrial']){
                $apiPlanInfo['endTrial'] = $endDate;
            }

            $apiTrialSearchList = $keywordModel->getSearchCountByReport($companyId, $userIds[self::PLAN_TYPE_API], self::PLAN_TYPE_API, $apiPlanInfo['startTrial'], $apiPlanInfo['endTrial'], true);
            //トライアル期間の検索がある場合
            if(!is_null($apiTrialSearchList)){

                //請求期間内にトライアル期間が含まれる場合のみ
                if( $apiPlanInfo['startTrial'] < $endDate && $apiPlanInfo['endTrial'] > $startDate){

                    foreach($apiTrialSearchList as $searchItem){

                        $unitPrice = $apiPlanInfo['trialSearchUnitPrice'];
                        $price = $apiPlanInfo['trialSearchUnitPrice'] * $searchItem['searchCount'];

                        $data[] = [
                            'userId' => $searchItem['userId'],
                            'userName' => $searchItem['name'].' (トライアル)',
                            'unitPrice' => $unitPrice,
                            'count' => $searchItem['searchCount'],
                            'price' => $price,
                            'contractStartDate' => $apiPlanInfo['startTrial'],
                            'contractEndDate' => $apiPlanInfo['endTrial'],
                            'chargeFlg' => $searchItem['chargeFlg'],
                            'planType' => self::PLAN_TYPE_API,
                        ];
                    }
                }
            }
        }

        return $data;
    }

}
