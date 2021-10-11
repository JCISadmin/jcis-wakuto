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
        $webAry = $userDetailModel->getDetail($claimList[0]->companyId, $claimList[0]->webPlanPlanId);
        $apiAry = $userDetailModel->getDetail($claimList[0]->companyId, $claimList[0]->apiPlanPlanId);
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
                    'companyId' => $claimList[0]->webPlanCompanyId,
                    'contractPlanName' => $claimList[0]->webPlanPlanName,
                    'contractTypeName' => $claimList[0]->webPlanTypeName,
                    'planType'=> $claimList[0]->webPlanPlanType,
                    'contractTypeId' => $claimList[0]->webPlanTypeId,
                    'ids' => $claimList[0]->webPlanIds,
                    'idUnitPrice' => $claimList[0]->webPlanIdUnitPrice,
                    'searchUnitPrice' => $claimList[0]->webPlanSearchUnitPrice,
                    'searchCount' => $claimList[0]->webPlanSearchCount,
                    'monthSearchCount' => $claimList[0]->webPlanMonthSearchCount,
                    'deposit' => $claimList[0]->webPlanDeposit,
                    'userDetail' => $webAry,
                    'charge' => $claimList[0]->items['web']['payPerUse']['price'],
                ],
                1 => [
                    'companyId' => $claimList[0]->apiPlanCompanyId,
                    'contractPlanName' => $claimList[0]->apiPlanPlanName,
                    'contractTypeName' => $claimList[0]->apiPlanTypeName,
                    'planType'=> $claimList[0]->apiPlanPlanType,
                    'contractTypeId' => $claimList[0]->apiPlanTypeId,
                    'ids' => $claimList[0]->apiPlanIds,
                    'idUnitPrice' => $claimList[0]->apiPlanIdUnitPrice,
                    'searchUnitPrice' => $claimList[0]->apiPlanSearchUnitPrice,
                    'searchCount' => $claimList[0]->apiPlanSearchCount,
                    'monthSearchCount' => $claimList[0]->apiPlanMonthSearchCount,
                    'deposit' => $claimList[0]->apiPlanDeposit,
                    'userDetail' => $apiAry,
                    'charge' => $claimList[0]->items['api']['payPerUse']['price'],
                ],
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/claim/edit',$assignAry);
    }

    /**
     * 更新
     *
     * @param UpdateRequest $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(UpdateRequest $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new TClaim;
        $cond = $request->session()->get(__CLASS__ . 'search');
        $companyId[] = $editId;
        $webPlanDeposit = null;
        $apiPlanDeposit = null;

        if ($request->has('deposit')) {
            /** @noinspection PhpUndefinedFieldInspection */
            foreach($request->deposit as $key => $value){
                if($key === 'web'){
                    $webPlanDeposit = $value;
                }elseif($key === 'api'){
                    $apiPlanDeposit = $value;
                }
            }
        }

        $claimData = $model->getList($cond['claimMonth'], $cond['companyName'], $companyId, null, false, false);
        /** @noinspection PhpUndefinedFieldInspection */
        $claimData[0]->paymentDate = $request->paymentDate;

        /** @noinspection PhpUndefinedFieldInspection */
        $claimData[0]->adjustNote = $request->adjustNote;

        /** @noinspection PhpUndefinedFieldInspection */
        $claimData[0]->adjustPrice = $request->adjustPrice;

        $claimData[0]->webPlanDeposit = $webPlanDeposit;
        $claimData[0]->apiPlanDeposit = $apiPlanDeposit;

        $model->claimUpdate($editId, $cond['claimMonth'], $cond['companyName'], $claimData);

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
            foreach($companyIds as $Id){

                $claimFlg = $TClaim->getClaimStatus($Id, $claimMonth, false);

                if( $claimFlg === true ){

                    continue;
                }

                $TClaim->changeClaimStatus($Id, $claimMonth);

                $companyId = [];
                $companyId[] = $Id;

                $fileName = sprintf('請求書-%s.pdf', $claimMonth);
                $item['filePath'] = $Claim->makePdf($companyId, $claimMonth, $fileName, true);
                $item['claimMonth'] = (new Datetime($claimMonth))->format('n');

                $list = $TClaim->getList($claimMonth, null, $companyId, '', false, false);

                $item['name'] = $list[0]->name;
                $item['claimName'] = $list[0]->claimName;

                $item['claimMailTo'] = explode(',', $list[0]->claimMailTo);
                $item['claimMailCc'] = explode(',', $list[0]->claimMailCc);

                Mail::to($item['claimMailTo'])
                    ->cc($item['claimMailCc'])
                    ->send(new ClaimMail($item));
            }
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

        $TClaim->changeClaimStatus($editId, $claimMonth);

        $companyId[] = $editId;
        $fileName = $Claim->getFileName($claimMonth);
        $item['filePath'] = $Claim->makePdf($companyId, $claimMonth, $fileName, true);
        $item['claimMonth'] = (new Datetime($claimMonth))->format('n');

        $list = $TClaim->getList($claimMonth, null, $companyId, '', false, false);

        $item['name'] = $list[0]->name;
        $item['claimName'] = $list[0]->claimName;

        $item['claimMailTo'] = explode(',', $list[0]->claimMailTo);
        $item['claimMailCc'] = explode(',', $list[0]->claimMailCc);

        Mail::to($item['claimMailTo'])
            ->cc($item['claimMailCc'])
            ->send(new ClaimMail($item));

        return redirect()->route('manageClaimEdit', [$editId]);
    }
}
