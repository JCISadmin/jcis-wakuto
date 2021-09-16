<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
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
use Exception;

/**
 * ユーザー管理画面
 */
class UserController extends Controller
{

    const TYPE_WEB = 'web';
    const TYPE_API = 'api';

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

        dd($userList);
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
    public function detail($editId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MUserCompany();
        $assignAry = [
            'userDetailList' => $model->get($editId),
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
    public function edit(Request $request, string $editId = ''): View|Factory|Application
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
            'contractTypeId' => 1,
            'contractTypeName' => '',
            'startTrial' => '',
            'useStartDate' => '',
            'useUpdateDate' => '',
            'useEndAlertDate' => '',
            'useEndDate' => '',
            'idUnitPrice' => '',
            'searchUnitPrice' => '',
            'searchCount' => '',
            'deposit' => '',
            'ids' => 0,
            'userDetail' => []
        ];

        if($editId == ''){
            //新規
            $webItems = $planItems;
            $apiItems = $planItems;
        }else{
            //更新
            $userDetailList = $userCompanyModel->get($editId);
            $userCompanyItems = $userDetailList['userCompany'];

            if(is_null($userDetailList['contractPlan']['web'])){
                $webItems = $planItems;
            }else{
                $webItems = $userDetailList['contractPlan']['web'];
            }

            if(is_null($userDetailList['contractPlan']['api'])){
                $apiItems = $planItems;
            }else{
                $apiItems = $userDetailList['contractPlan']['api'];
            }
        }

        //契約プランリスト
        $data = $contractPlanModel->getSelectList();
        $webAry = [];
        $apiAry = [];
        foreach($data as $key => $item){
            if($item->planType === self::TYPE_WEB){
                $webAry[$key] = $item;
            }elseif($item->planType === self::TYPE_API){
                $apiAry[$key] = $item;
            }
        }
        $contractPlanList = [
            'web' => $webAry,
            'api' => $apiAry,
        ];     
  


        $assignAry = [
            'editId' => $editId,
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

        } else {
            // 更新
            $model->upd($data);
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        }

        return redirect()->route('manageUserEdit', ['editId' => $data['editId']]);
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
}
