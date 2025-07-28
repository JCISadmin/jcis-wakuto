<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MAdminUser;
use App\Http\Requests\Manage\AdminUser\SearchRequest;
use App\Http\Requests\Manage\AdminUser\UpdateRequest;

/**
 * 管理ユーザー一覧
 */
class AdminUserController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['userId'] = '';
            $cond['userName'] = '';
        }

        $agentinfo = $request->session()->get('agentinfo'); 
        $agent_cd = $agentinfo["agent_cd"];
        $distributor_cd = $agentinfo["distributor_cd"];
        $level = $agentinfo["level"];

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new MAdminUser();
        $userList = $model->getList(
            $cond['userId'],
            $cond['userName'],
            $pageNum,
			$agent_cd
        );

        $assignAry = [
            'userId' => $cond['userId'],
            'userName' => $cond['userName'],
            'userList' => $userList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/adminUser/list', $assignAry);
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

        return redirect()->route('manageAdminUser');

    }

    /**
     * 更新アクション
     *
     * @param UpdateRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $agentinfo = $request->session()->get('agentinfo'); 
        $agent_cd = $agentinfo["agent_cd"];
        $distributor_cd = $agentinfo["distributor_cd"];
        $level = $agentinfo["level"];

        $data = $request->all();
        $model = new MAdminUser();

        try {
            $model->updateUser($data, $agent_cd);
        } catch (Exception $ex) {
            if ($ex->getMessage() != 'duplicate') {
                throw $ex;
            }
            return back()->withInput()->withErrors(['message' => '管理者IDが重複しています。']);
        }

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        return redirect()->route('manageAdminUser');
    }


}
