<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Mail\UserInfo;
use App\Mail\ZipPasswordInfo;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MContractStatus;
use App\Models\MContractPlan;
use App\Models\MContractType;
use App\Http\Requests\Manage\User\UpdateRequest;
use App\Models\Report;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Manage\User\SearchReport\SearchRequest;
use App\Models\TContractPlan;
use App\Models\TContractPlanDetail;
use App\Models\PdfSearchReport;
use DateTime;
use Illuminate\Support\Facades\Log;

/**
 * ユーザー管理画面
 */
class UserController extends Controller
{

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';

    const DATE_LOW_VALUE = '2000-01-01';
    const DATE_HIGH_VALUE = '3000-01-01';

    const INSERT_SEQ_NO = 1;

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['companyName'] = '';
            $cond['contractStatus'] = '';
            $cond['contractPlan'] = '';
            $cond['useEndAlertDate'] = '';
        }

        // ページ行数保持
        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        // ページ番号保持
        $pageNo = $request->input('page', '');
        $request->session()->put(__CLASS__ . 'pageNo', $pageNo);

        $contractStatusModel = new MContractStatus();
        $contractPlanModel = new MContractPlan();
        $userModel = new MUserCompany();

        $userList = $userModel->getList(
            $cond['companyName'],
            $cond['contractStatus'],
            $cond['contractPlan'],
            $cond['useEndAlertDate'],
            $pageNum
        );

        $assignAry = [
            'companyName' => $cond['companyName'],
            'contractStatus' => $cond['contractStatus'],
            'contractPlan' => $cond['contractPlan'],
            'useEndAlertDate' => $cond['useEndAlertDate'],
            'userList' => $userList,
            'selectList' => [
                'contractStatus' => $contractStatusModel->getSelectList(),
                'contractPlan' => $contractPlanModel->getSelectList(),
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/user/list', $assignAry);

    }


    /**
     * 検索アクション
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);
        $request->session()->put(__CLASS__ . 'pageNo', '');

        return redirect()->route('manageUser');
    }

    /**
     * ユーザー詳細画面表示
     *
     * @param Request $request
     * @param $editId
     * @param string $seqNo
     * @return Application|Factory|View
     */
    public function detail(Request $request, $editId, $seqNo = ''): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MUserCompany();
        $assignAry = [
            'userDetailList' => $model->get($editId, $seqNo),
            'pageNo' => $request->session()->get(__CLASS__ . 'pageNo')
        ];

        return view('manage/user/detail',$assignAry);
    }

    /**
     * ユーザー編集画面表示
     *
     * @param Request $request
     * @param string $editId
     * @param string $seqNo
     * @return Application|Factory|View
     */
    public function edit(Request $request, string $editId = '', $seqNo = ''): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $userCompanyModel = new MUserCompany();
        $contractStatusModel = new MContractStatus();
        $contractPlanModel = new MContractPlan();
        $contractTypeModel = new MContractType();
        $contractDetailModel = new TContractPlanDetail();

        $userCompanyItems = [
            'contractStatus' => '',
            'contractStatusName' => '',
            'chargeName' => '',
            'chargeMail' => '',
            'name' => '',
            'kana' => '',
            'companyId' => '',
            'postCode' => '',
            'address' => '',
            'tel' => '',
            'staffName' => '',
            'staffDepartmentJob' => '',
            'staffTel' => '',
            'staffMail' => '',
            'claimName' => '',
            'claimDepartmentJob' => '',
            'claimTel' => '',
            'claimMailTo' => '',
            'claimMailCc' => '',
            'claimMailBcc' => '',
            'paymentTerm' => '',
            'deliveryDate' => '',
        ];

        $planItems = [
            'contractPlanId' => '',
            'contractPlanName' => '',
            'planType' => '',
            'idPrice' => '',
            'unitPrice' => '',
            'startTrial' => '',
            'useStartDate' => '',
            'useUpdateDate' => '',
            'useEndAlertDate' => '',
            'useEndDate' => '',
            'deposit' => '',
            'trialSearchUnitPrice' => '',
            'contractDetail' => [
                "contractPlanName" => '',
                "planType" => '',
                "idPrice" => '',
                "unitPrice" => '',
                "contractTypeId" => 1,
                "contractTypeName" => '',
                "idUnitPrice" => '',
                "searchUnitPrice" => '',
                "searchCount" => '',
            ],
            'userDetail' => [],
            'ids' => 0
        ];

        $webAry = [];
        $apiAry = [];
        $noContract = (object)[
            'contractPlanId' => '',
            'name' => '契約なし',
            'idPrice' => '',
            'unitPrice' => '',
        ];

        if($editId == ''){
            //新規
            $webItems = $planItems;
            $webAry[] = $noContract;
            $apiItems = $planItems;
            $apiAry[] = $noContract;
        }else{
            //更新
            $userDetailList = $userCompanyModel->get($editId, $seqNo);
            $userCompanyItems = $userDetailList['userCompany'];

            if(is_null($userDetailList['contractPlan']['web'])){
                $webItems = $planItems;
                $webAry[] = $noContract;
            }else{
                $webItems = $userDetailList['contractPlan']['web'];
            }

            if(is_null($userDetailList['contractPlan']['api'])){
                $apiItems = $planItems;
                $apiAry[] = $noContract;
            }else{
                $apiItems = $userDetailList['contractPlan']['api'];
            }
        }

        //契約プランリスト
        $data = $contractPlanModel->getSelectList();
        foreach($data as $item){
            if($item->planType === self::TYPE_WEB){
                $webAry[] = $item;
            }elseif($item->planType === self::TYPE_API){
                $apiAry[] = $item;
            }
        }

        $contractPlanList = [
            'web' => $webAry,
            'api' => $apiAry,
        ];

        if($seqNo === ''){
            $seqNo = $contractDetailModel->getMaxSeqNo($editId);
        }

        $count = $contractDetailModel->getDataCount($editId);
        $maxSeqNo = $contractDetailModel->getMaxSeqNo($editId);
        $isContract = false;

        //既存データ有り
        if($count > 0){
            //編集データが最新データの場合
            if($seqNo == $maxSeqNo){
                //契約更新ボタンを表示
                $isContract = true;
            }
        }

        $assignAry = [
            'editId' => $editId,
            'seqNo' => $seqNo,
            'isContract' => $isContract,
            'userDetailList' => [
                'userCompany' => $userCompanyItems,
                'contractPlan' => [
                    'web' => $webItems,
                    'api' => $apiItems,
                ]
            ],
            'selectList' => [
                'contractStatus' => $contractStatusModel->getSelectList(),
                'contractPlan' => $contractPlanList,
                'contractType' => $contractTypeModel->getSelectList(),
                'paymentTerm' => config('hds.user.paymentTerm'),
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/user/edit', $assignAry);
    }

    /**
     * 更新処理
     *
     * @param UpdateRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MUserCompany();
        if ($data['editId'] == '') {
            // 新規
            $model->ins($data);
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_INS_SUCCESS'));
            $data['editId'] = $data['userCompany']['companyId'];
            $data['seqNo'] = self::INSERT_SEQ_NO;
        } else {
            // 更新
            $model->upd($data, $data['seqNo']);
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));
        }

        return redirect()->route('manageUserEdit', ['editId' => $data['editId'], 'seqNo' => $data['seqNo']]);
    }

    /**
     * パスワード変更
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function changePassword(Request $request): JsonResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MUserDetail();
        $password = $model->changePassword(
            $request->input('companyId'),
            $request->input('contractPlanId'),
            $request->input('userId')
        );

        return response()->json(['password' => $password]);

    }

    /**
     * ユーザー情報メール通知
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendUserInfo(Request $request): JsonResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->input();

        $userModel = new MUserDetail();

        $user = $userModel->get($data['companyId'], $data['contractPlanId'], $data['userId']);

        // zipファイル解答のためのパスワード生成
        $zipPassword = $userModel->makePassword();
        $data['zipPassword'] = $zipPassword;

        $user['idMailBcc'] = explode(',', $user['idMailBcc']);

        Mail::to($user['mail'])->send(new UserInfo($data));
        Mail::to($user['mail'])->send(new ZipPasswordInfo($data));
        //BCCメールアドレスが送信可能か
        $isSendableBcc = $this->isSendable($user['idMailBcc']);
        if($isSendableBcc){
            Mail::bcc($user['idMailBcc'])->send(new UserInfo($data));
            Mail::bcc($user['idMailBcc'])->send(new ZipPasswordInfo($data));
        }

        return response()->json(['result' => 'ok']);
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
    /**
     * 月別検索数PDFの生成
     *
     * @param Request $request
     * @param $editId
     * @return string
     */
    public function searchReportPdf(Request $request, $editId): string
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $cond = $request->session()->get(__CLASS__ . 'searchReport');
        if (empty($cond)) {
            $cond['useMonth'] = '';
            $cond['dispType'] = 'all';
        }
        
        $model = new PdfSearchReport();
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');

        $fileName = $model->getFileName($editId);
        $string = $model->makePdf($fileName, $editId, $cond['dispType'], $cond['useMonth'], $pageNo);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/pdf');
        header("Content-Disposition: inline; filename=\"$fileName\"");

        return $string;
    }

    /**
     * 月別検索数画面 画面表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function searchReport(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'searchReport');
        if (empty($cond)) {
            $cond['dispType'] = 'all';
            $cond['useMonth'] = '';
        }

        //現在日時
        $now = new Datetime();
        $date = $now->format('Y年n月j日H時i分');

        //会社名
        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($editId);

        //表示データ取得
        $pageNo = is_null($request->input('page')) ? 1 : $request->input('page');
        $model = new Report();

        $pageInfo = null;
        $pageData= null;

        //全件指定
        if($cond['dispType'] === 'all'){

            $pageInfo = $model->getReportPageInfo($editId, $pageNo, 'manage');
            $pageData = $pageInfo['pageData'];
            $pageAry = $pageData->items();
            $pageItem = array_values($pageAry);

            $year = null;
            if(!empty($pageItem)){
                $year = $pageItem[0];
            }
    
            $data = $model->getReportData($editId, $year);

            //月別情報が1つも無い場合、表を非表示
            if(empty($data['month'])){
                $detail = null;
            }else{
                $detail = [
                    'month' => $data['month'],
                    'year' => $data['year'],
                ];
            }

        //月別指定
        }elseif($cond['dispType'] === 'month'){

            $useMonth = new DateTime($cond['useMonth']);
            //指定月が現在より先
            if($now < $useMonth){
                $detail = null;
            }else{
                $useY = $useMonth->format('Y');
                $useYM = $useMonth->format('Y-m');

                $data = $model->getReportData($editId, $useY, $useMonth);

                //指定月情報が一つも無い場合、表を非表示
                if(!isset($data['month'][$useY][$useYM])){
                    $detail = null;
                }else{

                    $detail['month'][$useY][$useYM] = $data['month'][$useY][$useYM];
                    $detail['year'][$useY] = $data['year'][$useY];
                }
            }
        }

        //デポジット情報
        $webDeposit = 0;
        $webUnitPrice = 0;
        $webRemainCount = 0;
        $apiDeposit = 0;
        $apiUnitPrice = 0;
        $apiRemainCount = 0;

        $tContractPlan = new TContractPlan();
        $webPlan = $tContractPlan->getPlan($editId, self::TYPE_WEB);
        $apiPlan = $tContractPlan->getPlan($editId, self::TYPE_API);
        //DBデポジット
        if(!is_null($webPlan)){
            $webDeposit = empty($webPlan['deposit']) ? 0 : $webPlan['deposit'];
            $webUnitPrice = empty($webPlan['contractDetail']['searchUnitPrice']) ? 1 : $webPlan['contractDetail']['searchUnitPrice'];
            $webRemainCount = ceil($webDeposit / $webUnitPrice);
        }
        //APIデポジット
        if(!is_null($apiPlan)){      
            $apiDeposit = empty($apiPlan['deposit']) ? 0 : $apiPlan['deposit'];
            $apiUnitPrice = empty($apiPlan['contractDetail']['searchUnitPrice']) ? 1 : $apiPlan['contractDetail']['searchUnitPrice'];
            $apiRemainCount = ceil($apiDeposit / $apiUnitPrice);
        }
            
        //今月検索件数/年間検索件数/デポジット検索欄
        $monthSearchCount = 0;
        $yearSearchCount = 0;
        $depositList['web'] = [];
        $depositList['api'] = [];
        $nowData = $model->getReportData($editId, $now->format('Y'));

        if(isset($nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'])){
            $monthSearchCount = $nowData['month'][$now->format('Y')][$now->format('Y-m')]['totalSearchCount'];
        }
        if(isset($nowData['year'][$now->format('Y')]['totalSearchCount'])){
            $yearSearchCount = $nowData['year'][$now->format('Y')]['totalSearchCount'];
        }
        if(isset($data['deposit'])){
            $depositList = $data['deposit'];
        }

        $assignAry = [
            'date' => $date,
            'companyId' => $editId,
            'companyName' => $companyName,
            'monthSearchCount' => $monthSearchCount,
            'yearSearchCount' => $yearSearchCount,
            'webDeposit' => $webDeposit,
            'apiDeposit' => $apiDeposit,
            'webRemainCount' => $webRemainCount,
            'apiRemainCount' => $apiRemainCount,
            'depositList' => $depositList,
            'pageList' => $pageData,
            'detail' => $detail,
            'useMonth' => $cond['useMonth'],
            'dispType' => $cond['dispType'],
            'pageNo' => $pageNo,
        ];

        return view('manage/user/searchReport',$assignAry);
    }

    /**
     * 月別検索数画面 検索
     *
     * @param SearchRequest $request
     * @param string $editId
     * @return RedirectResponse
     */
    public function searchSearchReport(SearchRequest $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'searchReport', $cond);

        //月別指定 かつ 月の入力無し
        if($cond['dispType'] === 'month' && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        //月別指定 かつ 指定月が現在よりも先
        if($cond['dispType'] === 'month' && new DateTime() < new DateTime($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '表示データがありません。']);
        }

        return redirect()->route('manageUserSearchReport', ['editId' => $editId]);
    }

    /**
     * 契約履歴一覧画面 初期表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function contractHistory(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $contractPlanDetail = new TContractPlanDetail();

        $list = $contractPlanDetail->getList($editId);

        foreach($list as $idx => $item){
            if($item->webPlanContractEndDate === self::DATE_HIGH_VALUE){
                $list[$idx]->webPlanContractEndDate = null;
            }
            if($item->apiPlanContractEndDate === self::DATE_HIGH_VALUE){
                $list[$idx]->apiPlanContractEndDate = null;
            }
        }

        $assignAry = [
            'editId' => $editId,
            'contractList' => $list,
        ];

        return view('manage/user/contractHistory',$assignAry);

    }

    /**
     * 契約更新処理(履歴追加)
     *
     * @param UpdateRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function contractUpdate(UpdateRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MUserCompany();

        //契約更新日が入力されているか
        if($data["contractStartDate"] === null){
            return back()->withInput()->withErrors(['message' => '契約更新日が設定されていません。']);
        }

        //editId NULLチェック
        if ($data['editId'] == '') {
            throw new Exception('editId NULL ERROR');
        }

        $model->upd($data, $data['seqNo'], true);
        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        $contractPlanDetail = new TContractPlanDetail();
        $maxSeqNo = $contractPlanDetail->getMaxSeqNo($data['userCompany']['companyId']);

        return redirect()->route('manageUserEdit', ['editId' => $data['editId'], 'seqNo' => $maxSeqNo ]);
    }

    /**
     * 契約削除(履歴削除)
     *
     * @param Request $request
     * @param string $editId
     * @return RedirectResponse
     */
    public function contractDelete(Request $request, $editId): RedirectResponse
    {
        $contractPlanDetail = new TContractPlanDetail();
        $contractPlanDetail->deletePlan($editId);

        return redirect()->route('manageUserContractHistory', ['editId' => $editId]);
    }
}
