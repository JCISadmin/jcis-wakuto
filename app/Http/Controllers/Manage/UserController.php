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
use App\Models\PdfSearchReport;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\Manage\User\SearchReport\SearchRequest;
use App\Models\TContractPlanDetail;
use DateTime;

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

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

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

        return redirect()->route('manageUser');
    }

    /**
     * ユーザー詳細画面表示
     *
     * @param $editId
     * @return Application|Factory|View
     */
    public function detail($editId, $seqNo = ''): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MUserCompany();
        $assignAry = [
            'userDetailList' => $model->get($editId, $seqNo),
        ];

        return view('manage/user/detail',$assignAry);
    }

    /**
     * ユーザー編集画面表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function edit(Request $request, string $editId = '', $seqNo = ''): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $userCompanyModel = new MUserCompany();
        $contractStatusModel = new MContractStatus();
        $contractPlanModel = new MContractPlan();
        $contractTypeModel = new MContractType();

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
        ];

        $planItems = [
            'contractPlanId' => '',
            'contractPlanName' => '',
            'planType' => '',
            'idPrice' => '',
            'unitPrice' => '',
            // 'contractTypeId' => 1,
            // 'contractTypeName' => '',
            'startTrial' => '',
            'useStartDate' => '',
            'useUpdateDate' => '',
            'useEndAlertDate' => '',
            'useEndDate' => '',
            // 'idUnitPrice' => '',
            // 'searchUnitPrice' => '',
            // 'searchCount' => '',
            'deposit' => '',
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

        $assignAry = [
            'editId' => $editId,
            'seqNo' => $seqNo,
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

        Mail::to($user['mail'])->send(new UserInfo($data));
        Mail::to($user['mail'])->send(new ZipPasswordInfo($data));

        return response()->json(['result' => 'ok']);
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

        $model = new PdfSearchReport();

        $fileName = $model->getFileName($editId);
        $string = $model->makePdf($editId, $fileName, $cond['dispType'], $cond['useMonth']);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $string;
    }

    /**
     * 月別検索数画面 初期表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function searchReport(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($editId);

        $model = new PdfSearchReport();
        $detail = $model->getReportData($editId);

        $assignAry = [
            'companyId' => $editId,
            'companyName' => $companyName,
            'detail' => $detail,
            'useMonth' => '',
            'dispType' => 'all',
        ];

        return view('manage/user/searchReport',$assignAry);
    }

    /**
     * 月別検索数画面 検索結果表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function listSearchReport(Request $request, $editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'searchReport');

        $userCompany = new MUserCompany();
        $companyName = $userCompany->getCompanyName($editId);

        $model = new PdfSearchReport();
        if($cond['dispType'] === 'all'){
            $detail = $model->getReportData($editId);
        }elseif($cond['dispType'] === 'month'){
            $detail = $model->getReportDatabyMonth($editId, $cond['useMonth']);
        }

        $assignAry = [
            'companyId' => $editId,
            'companyName' => $companyName,
            'detail' => $detail,
            'useMonth' => $cond['useMonth'],
            'dispType' => $cond['dispType'],
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

        if($cond['dispType'] === 'month' && is_null($cond['useMonth'])){
            return back()->withInput()->withErrors(['message' => '利用年月が指定されていません。']);
        }

        $userCompany = new MUserCompany();
        $createMonth = $userCompany->getCreateMonth($editId);

        if( new DateTime() < new DateTime($cond['useMonth']) || new DateTime($cond['useMonth']) < new DateTime($createMonth)){
            return back()->withInput()->withErrors(['message' => '表示データがありません。']);
        }

        return redirect()->route('manageUserListSearchReport', ['editId' => $editId]);
    }

    /**
     * 月別検索数画面 初期表示
     *
     * @param Request $request
     * @param string $editId
     * @return Application|Factory|View
     */
    public function changeHistory(Request $request, $editId): View|Factory|Application
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

        return view('manage/user/changeHistory',$assignAry);

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
        
        return redirect()->route('manageUserEdit', ['editId' => $data['editId'], 'seqNo' => $data['seqNo']+1 ]);
    }

}