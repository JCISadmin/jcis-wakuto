<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Manage\Claim\SearchRequest;
use App\Http\Requests\Manage\Claim\UpdateRequest;
use App\Models\Claim;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Models\CsvClaim;
use App\Models\TClaim;
use App\Models\TClaimDetail;
use App\Models\MUserDetail;
use App\Models\TKeywordHistory;
use Datetime;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClaimMail;
use App\Models\BaseModel;
use App\Models\MContractType;
use App\Models\Report;

/**
 * 請求一覧
 */
class ClaimController extends Controller
{

    /**
     * 初期表示
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $assignAry = [
            'claimMonth' => '',
            'companyName' => '',
        ];

        return view('manage/claim/list', $assignAry);
    }

    /**
     * 検索結果表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function list(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new TClaim;
        $claimList = $model->getList($cond['claimMonth'], $cond['companyName'], null, $pageNum, true, false);

        $assignAry = [
            'claimMonth' => $cond['claimMonth'],
            'companyName' => $cond['companyName'],
            'claimList' => $claimList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/claim/list', $assignAry);
    }

    /**
     * 検索アクション
     *
     * @param SearchRequest $request
     * @return RedirectResponse
     */
    public function search(SearchRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);

        return redirect()->route('manageClaimList');
    }

    /**
     * 未請求ボタンをクリック
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function claim(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changeClaimStatus($editId, $claimMonth);

        /** @noinspection PhpUndefinedFieldInspection */
        if ($request->from === "edit") {
            return redirect()->route('manageClaimEdit', ['editId' => $editId]);
        }

        $page = $request->input('page');

        return redirect()->route('manageClaimList', ['page' => $page]);
    }

    /**
     * 請求済ボタンをクリック
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function notClaim(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changeNotClaimStatus($editId, $claimMonth);

        /** @noinspection PhpUndefinedFieldInspection */
        if ($request->from === "edit") {
            return redirect()->route('manageClaimEdit', ['editId' => $editId]);
        }

        $page = $request->input('page');

        return redirect()->route('manageClaimList', ['page' => $page]);
    }

    /**
     * 未入金ボタンをクリック
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function payment(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changePaymentStatus($editId, $claimMonth);

        /** @noinspection PhpUndefinedFieldInspection */
        if ($request->from === "edit") {
            return redirect()->route('manageClaimEdit', ['editId' => $editId]);
        }

        $page = $request->input('page');

        return redirect()->route('manageClaimList', ['page' => $page]);
    }

    /**
     * 入金済ボタンをクリック
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function notPayment(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changeNotPaymentStatus($editId, $claimMonth);

        /** @noinspection PhpUndefinedFieldInspection */
        if ($request->from === "edit") {
            return redirect()->route('manageClaimEdit', ['editId' => $editId]);
        }

        $page = $request->input('page');

        return redirect()->route('manageClaimList', ['page' => $page]);
    }

    /**
     * エクスポート
     *
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function export(Request $request): BinaryFileResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new CsvClaim();

        /** @noinspection PhpUndefinedFieldInspection */
        $csvInfo = $model->makeCsv($claimMonth, $request->exportFlg);
        $headers = [['Content-Type' => 'text/css']];

        return response()->download($csvInfo['filePath'], $csvInfo['fileName'], $headers)->deleteFileAfterSend(true);
    }

    /**
     * 編集画面を表示
     *
     * @param Request $request
     * @param $editId
     * @return Application|Factory|View
     * @throws Exception
     */
    public function edit(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimModel = new Claim();
        $tClaimModel = new TClaim();
        $tClaimDetailModel = new TClaimDetail();
        $keywordModel =new TKeywordHistory();
        $userDetailModel = new MUserDetail();
        $mContractTypeModel = new MContractType();
        $reportModel = new Report();

        $cond = $request->session()->get(__CLASS__ . 'search');
        $companyId[] = $editId;
        $claimList = $tClaimModel->getList($cond['claimMonth'], $cond['companyName'], $companyId, null, false, false, true);
        $webAry = $userDetailModel->getDetail($claimList[0]->companyId, $claimList[0]->webContractPlanId);
        $apiAry = $userDetailModel->getDetail($claimList[0]->companyId, $claimList[0]->apiContractPlanId);
        $year = date_format(new DateTime($cond['claimMonth']), 'Y');
        $month = date_format(new DateTime($cond['claimMonth']), 'm');

        if(is_null($webAry) === false){
            foreach($webAry as $key => $value ){
                $webAry[$key]['no'] = $key + 1;
                $webAry[$key]['monthSearchCount'] = $keywordModel->getMonthSearchCount($claimList[0]->companyId, $value['userId'], $value['contractPlanId'], $year, $month);
            }
        }

        if(is_null($webAry) === false){
            foreach($apiAry as $key => $value ){
                $apiAry[$key]['no'] = $key + 1;
                $apiAry[$key]['monthSearchCount'] = $keywordModel->getMonthSearchCount($claimList[0]->companyId, $value['userId'], $value['contractPlanId'], $year, $month);
            }
        }

        //新規登録時 
        if(is_null($claimList[0]->paymentDate)){
            //発行日（編集当日）をセット
            $claimList[0]->claimDate = date('Y-m-d');
            if(is_null($claimList[0]->paymentTerm)){
                //支払期日（請求翌月末）をセット
                $claimList[0]->paymentDate = date('Y-m-d', strtotime('last day of next month' . $cond['claimMonth']));
            }else{
                //支払期日（ユーザー詳細 設定値）をセット
                $claimList[0]->paymentDate = new DateTime($cond['claimMonth']);
                $claimList[0]->paymentDate->modify(config('hds.user.paymentTerm.'.$claimList[0]->paymentTerm.'.modify'));
                $claimList[0]->paymentDate = $claimList[0]->paymentDate->format('Y-m-d');
            }
        }

        //tClaimDetailテーブルから費目情報(補正額以外)を取得
        $expenseList = $tClaimDetailModel->getExpenseList($editId, $cond['claimMonth']);
        //DBから取得できない場合、費目情報を計算して取得
        if($expenseList === []){
            $expenseList = $claimModel->getExpenseList($companyId, $cond['claimMonth']);
        }

        //tClaimDetailテーブルから費目情報(補正額)を取得
        $expenseAdjustList = $tClaimDetailModel->getExpenseAdjustList($editId, $cond['claimMonth']);

        //既存データなし
        $count = $tClaimModel->countClaimData($companyId, $cond['claimMonth']);
        if($count <= 0){
            //備考欄の初期値を設定
            $claimList[0]->claimNote = config('note.claim.claimNote');
        }

        if(is_null($claimList[0]->claimDeliveryDate)){
            $claimList[0]->claimDeliveryDate = $claimList[0]->deliveryDate;
        }

        $searchData = $claimModel->getSearchDetail($editId, $cond['claimMonth']);

        //契約履歴表示欄
        $webContractList = [];
        foreach($claimList[0]->webContractInfo as $webContractItem){
            $webContractList[] = [
                'companyId' => $claimList[0]->webCompanyId,
                'contractStartDate' =>$webContractItem['contractStartDate'],
                'contractEndDate' => $webContractItem['contractEndDate'],
                'contractPlanId' => $claimList[0]->webContractPlanId,
                'contractPlanName' => $claimList[0]->webContractPlanName,
                'contractTypeName' => $mContractTypeModel->getTypeNameByTypeId($webContractItem['contractTypeId']),
                'planType'=> $claimList[0]->webPlanType,
                'contractTypeId' => $webContractItem['contractTypeId'],
                'ids' => $claimList[0]->webIds,
                'idUnitPrice' => $webContractItem['idUnitPrice'],
                'searchUnitPrice' => $webContractItem['searchUnitPrice'],
                'searchCount' => $webContractItem['searchCount'],
            ];
        }
        $apiContractList = [];
        foreach($claimList[0]->apiContractInfo as $apiContractItem){
            $apiContractList[] = [
                'companyId' => $claimList[0]->webCompanyId,
                'contractStartDate' =>$apiContractItem['contractStartDate'],
                'contractEndDate' => $apiContractItem['contractEndDate'],
                'contractPlanId' => $claimList[0]->webContractPlanId,
                'contractPlanName' => $claimList[0]->webContractPlanName,
                'contractTypeName' => $mContractTypeModel->getTypeNameByTypeId($apiContractItem['contractTypeId']),
                'planType'=> $claimList[0]->webPlanType,
                'contractTypeId' => $apiContractItem['contractTypeId'],
                'ids' => $claimList[0]->webIds,
                'idUnitPrice' => $apiContractItem['idUnitPrice'],
                'searchUnitPrice' => $apiContractItem['searchUnitPrice'],
                'searchCount' => $apiContractItem['searchCount'],
            ];
        }

        //検索数表示欄
        $searchList[BaseModel::PLAN_TYPE_WEB] = [];
        $searchList[BaseModel::PLAN_TYPE_API] = [];
        foreach($searchData['searchList'] as $searchItem){
            $searchList[$searchItem['planType']][] = [
                'user' => $searchItem['user'],
                'contractStartDate' =>  $searchItem['contractStartDate'],
                'contractEndDate' =>  $searchItem['contractEndDate'],
                'unitPrice' =>  $searchItem['unitPrice'],
                'count' =>  $searchItem['count'],
                'price' =>  $searchItem['price'],
            ];
        }

        $assignAry = [
            'claimMonth' => $cond['claimMonth'],
            'claimList' => $claimList,
            'expenseList' => $expenseList,
            'expenseAdjustList' => $expenseAdjustList,
            'planList' => [
                0 => [
                        'contractList' => $webContractList,
                        'searchList' => $searchList[BaseModel::PLAN_TYPE_WEB],
                        'totalCount' => $searchData[BaseModel::PLAN_TYPE_WEB]['totalSearchCount'],
                        'totalPrice' => $searchData[BaseModel::PLAN_TYPE_WEB]['totalSearchPrice'],
                        'planType' => $claimList[0]->webPlanType,
                        'contractTypeId' => $claimList[0]->webContractTypeId,
                        'deposit' => $claimList[0]->webDeposit,
                ],
                1 => [
                        'contractList' => $apiContractList,
                        'searchList' => $searchList[BaseModel::PLAN_TYPE_API],
                        'totalCount' => $searchData[BaseModel::PLAN_TYPE_API]['totalSearchCount'],
                        'totalPrice' => $searchData[BaseModel::PLAN_TYPE_API]['totalSearchPrice'],
                        'planType' => $claimList[0]->apiPlanType,
                        'contractTypeId' => $claimList[0]->apiContractTypeId,
                        'deposit' => $claimList[0]->apiDeposit,
                ],
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        //全額デポジットの場合、表示データ配列にデポジット不足項目の表示値を追加
        if($claimList[0]->webContractTypeId === TClaim::TYPE_ALL_DEPOSIT){
            $assignAry['planList'][0]['overageCharges'] = $claimList[0]->items['web']['overageCharges'];
        }

        if($claimList[0]->apiContractTypeId === TClaim::TYPE_ALL_DEPOSIT){
            $assignAry['planList'][1]['overageCharges'] = $claimList[0]->items['api']['overageCharges'];
        }

        return view('manage/claim/edit',$assignAry);
    }

    /**
     * 更新
     *
     * @param UpdateRequest $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     * 
     */
    public function update(UpdateRequest $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $tClaimModel = new TClaim();
        $tClaimDetailModel = new TClaimDetail();
        $cond = $request->session()->get(__CLASS__ . 'search');
        $webDeposit = null;
        $apiDeposit = null;

        if ($request->has(BaseModel::PLAN_TYPE_WEB)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $webInfo = $request->{BaseModel::PLAN_TYPE_WEB};
            $webDeposit = $webInfo['deposit'];
        }

        if ($request->has(BaseModel::PLAN_TYPE_API)) {
            /** @noinspection PhpUndefinedFieldInspection */
        $apiInfo = $request->{BaseModel::PLAN_TYPE_API};
            $apiDeposit = $apiInfo['deposit'];
        }

        /** @noinspection PhpUndefinedFieldInspection */
        $updateData = [
            'claimDate' => $request->claimDate,
            'paymentDate' => $request->paymentDate,
            'deliveryDate' => $request->deliveryDate,
            'detail' => $request->detail,
            'claimNote' => $request->claimNote,
            'webDeposit' => $webDeposit,
            'apiDeposit' => $apiDeposit,
            'memo' => $request->memo,
        ];

        $tClaimDetailModel->claimUpdate($editId, $cond['claimMonth'], $updateData);
        $tClaimModel->claimUpdate($editId, $cond['claimMonth'], $updateData);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        return redirect()->route('manageClaimEdit', ['editId' => $editId]);
    }

    /**
     * 請求書プレビュー
     *
     * @param Request $request
     * @param $editId
     * @return string
     * @throws Exception
     */
    public function pdf(Request $request, $editId): string
    {
        $model = new Claim();

        $companyId[] = $editId;
        $cond = $request->session()->get(__CLASS__ . 'search');
        $fileName = $model->getFileName($cond['claimMonth']);
        $string = $model->makePdf($companyId, $cond['claimMonth'], $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"$fileName\"");

        return $string;
    }

    /**
     * 一括メール送信
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     * @noinspection PhpUndefinedFieldInspection
     */
    public function bulkMail(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $TClaim = new TClaim();
        $Claim = new Claim();
        $companyIds = $request->exportFlg;
        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $item = [];

        if (is_null($companyIds) === false) {
            //メール送信前に、請求先TOの登録チェックを実施
            $errCompanys = '';
            $i = 1;

            foreach($companyIds as $id){
                $companyId = [];//リセット
                $companyId[] = $id;
                $list = $TClaim->getList($claimMonth, null, $companyId, null, false, false);

                $item['claimMailTo'] = explode(',', $list[0]->claimMailTo);
                $isSendableTo = $this->isSendable($item['claimMailTo']);
                if($isSendableTo === false){
                    if($i == 1){
                        $errCompanys = $id;
                    }else{
                        $errCompanys = $errCompanys.'/'.$id;
                    }
                    $i++;
                }
            }

            if(empty($errCompanys) === false){
                $request->session()->flash(__CLASS__ . 'msg', sprintf(__('messages.INF_PREASE_REGIST_CLAIM_MAIL_TOS'),$errCompanys));
                return redirect()->route('manageClaimList');
            }

            //メール送信処理
            foreach($companyIds as $Id){

                //請求済の場合、処理をスキップ
                $claimFlg = $TClaim->getClaimStatus($Id, $claimMonth);
                if( $claimFlg === true ){
                    continue;
                }

                //請求金額が0の場合、処理をスキップ
                $companyId = [];//リセット
                $companyId[] = $Id;
                $list = $TClaim->getList($claimMonth, null, $companyId, null, false, false);
                if( $list[0]->priceWithTax == 0 ){
                    continue;
                }

                //請求データを更新
                $TClaim->changeClaimStatus($Id, $claimMonth);

                //請求書PDFを作成
                $fileName = sprintf('請求書-%s.pdf', $claimMonth);
                $item['filePath'] = $Claim->makePdf($companyId, $claimMonth, $fileName, true);
                $item['claimMonth'] = (new Datetime($claimMonth))->format('n');

                $item['name'] = $list[0]->name;
                $item['claimName'] = $list[0]->claimName;

                $item['claimMailTo'] = explode(',', $list[0]->claimMailTo);
                $item['claimMailCc'] = explode(',', $list[0]->claimMailCc);
                $item['claimMailBcc'] = $list[0]->claimMailBcc;
                $isSendableCc = $this->isSendable($item['claimMailCc']);

                //メール送信
                if($isSendableCc){
                    Mail::to($item['claimMailTo'])
                        ->cc($item['claimMailCc'])
                        ->bcc($item['claimMailBcc'])
                        ->send(new ClaimMail($item));
                }else{
                    Mail::to($item['claimMailTo'])->send(new ClaimMail($item));
                }
            }
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_SEND_CLAIMMAIL'));
        } else {
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_NOT_CHECK'));
        }

        return redirect()->route('manageClaimList');

    }

    /**
     * メール送信
     *
     * @param $editId
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function mail($editId, Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');

        $TClaim = new TClaim();
        $Claim = new Claim();

        //請求金額が0の場合、メール送信を中止
        $companyId[] = $editId;
        $list = $TClaim->getList($claimMonth, null, $companyId, null, false, false);
        if( $list[0]->priceWithTax == 0 ){

            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_NOT_SEND_CLAIMMAIL'));
            return redirect()->route('manageClaimEdit', [$editId]);
        }

        //請求先TOが未登録の場合、メール送信を中止
        $item['claimMailTo'] = explode(',', $list[0]->claimMailTo);
        $isSendableTo = $this->isSendable($item['claimMailTo']);
        if($isSendableTo === false){

            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_PREASE_REGIST_CLAIM_MAIL_TO'));
            return redirect()->route('manageClaimEdit', [$editId]);
        }

        //請求データを更新
        $TClaim->changeClaimStatus($editId, $claimMonth);

        //請求書PDFを作成
        $fileName = $Claim->getFileName($claimMonth);
        $item['filePath'] = $Claim->makePdf($companyId, $claimMonth, $fileName, true);
        $item['claimMonth'] = (new Datetime($claimMonth))->format('n');

        $item['name'] = $list[0]->name;
        $item['claimName'] = $list[0]->claimName;

        $item['claimMailCc'] = explode(',', $list[0]->claimMailCc);
        $isSendableCc = $this->isSendable($item['claimMailCc']);
        $item['claimMailBcc'] = explode(',', $list[0]->claimMailBcc);
        $isSendableBcc = $this->isSendable($item['claimMailBcc']);

        //差出人メールアドレスを担当窓口のものに変更
        config(['mail.from.address' => $list[0]->chargeMail]);

        //メール送信
        if($isSendableCc && $isSendableBcc){
            Mail::to($item['claimMailTo'])->send(new ClaimMail($item));
            Mail::cc($item['claimMailCc'])->send(new ClaimMail($item));
            Mail::bcc($item['claimMailBcc'])->send(new ClaimMail($item));
        }else{
            Mail::to($item['claimMailTo'])->send(new ClaimMail($item));
        }

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_SEND_CLAIMMAIL'));
        return redirect()->route('manageClaimEdit', [$editId]);
    }

    /**
     * メールアドレスが送信可能かチェック
     *
     * @param $mailAddressAry
     * @return $isSendable
     */
    public function isSendable($mailAddressAry)
    {
        $isSendable = true;
        
        foreach($mailAddressAry as $mailAddress){
            if(empty($mailAddress)){
                $isSendable = false;
            }
        }

        return $isSendable;
    }
}