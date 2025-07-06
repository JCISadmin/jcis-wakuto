<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Mail\UserInfo;
use App\Mail\ZipPasswordInfo;
use App\Models\MUserCompany;
use App\Models\MUserDetail;
use App\Models\TKeywordHistory;
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
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Models\TContractPlanDetail;
use App\Models\MCompany;
use App\Models\BaseModel;

/**
 * ユーザー管理画面
 */
class UserController extends Controller
{
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
		// 販売店・代理店情報
        $agentinfo = $request->session()->get('agentinfo'); 
        $agent_cd = $agentinfo["agent_cd"];
        $distributor_cd = $agentinfo["distributor_cd"];
        $level = $agentinfo["level"];

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
            $pageNum,
			$agentinfo
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
            'freeFlg' => '',
            'freePeriod' => '',
            'memo' => '',
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
            $userDetailList['allowIpList'] = [];
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
            if($item->planType === BaseModel::PLAN_TYPE_WEB){
                $webAry[] = $item;
            }elseif($item->planType === BaseModel::PLAN_TYPE_API){
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
                'allowIpList' => $userDetailList['allowIpList'],
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

        $mail = Mail::to($user['mail']);
        //BCCメールアドレスが送信可能か
        $isSendableBcc = $this->isSendable($user['idMailBcc']);
        if($isSendableBcc){
            $mail->bcc($user['idMailBcc']);
        }

        //フッターにmCompanyテーブルの値を使用
        $mCompanyModel = new MCompany();
        $companyInfo = $mCompanyModel->getCompanyInfo();
        $data['companyInfo'] = $companyInfo;

        $mail->send(new UserInfo($data));
        $mail->send(new ZipPasswordInfo($data));

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
     * 多重ログイン解除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function releaseLogin(Request $request): JsonResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->input();

        $userModel = new MUserDetail();
        $userModel->updateLoginTime($data['companyId'], $data['contractPlanId'], $data['userId'], NULL);

        return response()->json(['result' => 'ok']);
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
            if($item->webPlanContractEndDate === BaseModel::DATE_HIGH_VALUE){
                $list[$idx]->webPlanContractEndDate = null;
            }
            if($item->apiPlanContractEndDate === BaseModel::DATE_HIGH_VALUE){
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

    /**
     * ユーザー削除
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteUser(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $dt = new \DateTime();
        $fromDate = $dt->format('Y-m-01 00:00:00');
        $dt = new \DateTime('last day of this month');
        $toDate = $dt->format('Y-m-d 23:59:59');

        $keywordModel = new TKeywordHistory();
        $isSearch = $keywordModel->isSearch($data['companyId'], $data['userId'], $fromDate, $toDate);
        if ($isSearch) {
            return back()->withInput()->withErrors(['message' => 'ユーザー削除に失敗しました。当月に有効な検索数があります。']);
        }

        $model = new MUserDetail();
        $model->deleteUser($data['companyId'], $data['contractPlanId'], $data['userId']);

        return redirect()->route('manageUserDetail', ['editId' => $data['companyId']]);
    }
}
