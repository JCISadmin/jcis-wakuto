<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\MContractStatus;
use App\Models\MContractPlan;

/**
 * ユーザー管理画面
 */
class UserController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $contractStatusModel = new MContractStatus();
        $contractPlanModel = new MContractPlan();

        $assignAry = [
            'selectList' => [
                'contractStatus' => $contractStatusModel->getSelectList(),
                'contractPlan' => $contractPlanModel->getSelectList(),
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];


        return view('manage/user/list', $assignAry);

    }

}
