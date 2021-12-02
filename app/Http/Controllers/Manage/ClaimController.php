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
use App\Models\MUserDetail;
use App\Models\TKeywordHistory;
use Datetime;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClaimMail;
use App\Models\BaseModel;

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

        return redirect()->route('manageClaimList');
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

        return redirect()->route('manageClaimList');
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

        $claimModel = new TClaim;
        $keywordModel =new TKeywordHistory();
        $userDetailModel = new MUserDetail();

        $cond = $request->session()->get(__CLASS__ . 'search');
        $companyId[] = $editId;
        $claimList = $claimModel->getList($cond['claimMonth'], $cond['companyName'], $companyId, null, false, false);
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

        $assignAry = [
            'claimMonth' => $cond['claimMonth'],
            'claimList' => $claimList,
            'planList' => [
                0 => [
                    'companyId' => $claimList[0]->webCompanyId,
                    'contractPlanId' => $claimList[0]->webContractPlanId,
                    'contractPlanName' => $claimList[0]->webContractPlanName,
                    'contractTypeName' => $claimList[0]->webContractTypeName,
                    'planType'=> $claimList[0]->webPlanType,
                    'contractTypeId' => $claimList[0]->webContractTypeId,
                    'ids' => $claimList[0]->webIds,
                    'idUnitPrice' => $claimList[0]->webIdUnitPrice,
                    'searchUnitPrice' => $claimList[0]->webSearchUnitPrice,
                    'searchCount' => $claimList[0]->webSearchCount,
                    'monthSearchCount' => $claimList[0]->webMonthSearchCount,
                    'deposit' => $claimList[0]->webDeposit,
                    'userDetail' => $webAry,
                ],
                1 => [
                    'companyId' => $claimList[0]->apiCompanyId,
                    'contractPlanId' => $claimList[0]->apiContractPlanId,
                    'contractPlanName' => $claimList[0]->apiContractPlanName,
                    'contractTypeName' => $claimList[0]->apiContractTypeName,
                    'planType'=> $claimList[0]->apiPlanType,
                    'contractTypeId' => $claimList[0]->apiContractTypeId,
                    'ids' => $claimList[0]->apiIds,
                    'idUnitPrice' => $claimList[0]->apiIdUnitPrice,
                    'searchUnitPrice' => $claimList[0]->apiSearchUnitPrice,
                    'searchCount' => $claimList[0]->apiSearchCount,
                    'monthSearchCount' => $claimList[0]->apiMonthSearchCount,
                    'deposit' => $claimList[0]->apiDeposit,
                    'userDetail' => $apiAry,
                ],
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        //全額デポジットの場合、表示データ配列にデポジット不足項目の表示値を追加
        if($claimList[0]->webContractTypeId === TClaim::TYPE_ALL_DEPOSIT){
            $assignAry['planList'][0]['overageCharges'] = $claimList[0]->items['web']['payPerUse']['overageCharges'];
        }

        if($claimList[0]->apiContractTypeId === TClaim::TYPE_ALL_DEPOSIT){
            $assignAry['planList'][1]['overageCharges'] = $claimList[0]->items['api']['payPerUse']['overageCharges'];
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

        $model = new TClaim;
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
            'paymentDate' => $request->paymentDate,
            'adjustNote' => $request->adjustNote,
            'adjustPrice' => $request->adjustPrice,
            'webDeposit' => $webDeposit,
            'apiDeposit' => $apiDeposit,
        ];

        $model->claimUpdate($editId, $cond['claimMonth'], $updateData);

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
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

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
                $isSendableCc = $this->isSendable($item['claimMailCc']);

                //メール送信
                if($isSendableCc){
                    Mail::to($item['claimMailTo'])
                        ->cc($item['claimMailCc'])
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

        //メール送信
        if($isSendableCc){
            Mail::to($item['claimMailTo'])
                ->cc($item['claimMailCc'])
                ->send(new ClaimMail($item));
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