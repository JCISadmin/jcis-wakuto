<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MAdminUser;
use App\Http\Requests\Manage\AdminUser\SearchRequest;
use App\Http\Requests\Manage\AdminUser\UpdateRequest;

class AdminUserController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'serach');
        if (empty($cond)) {
            $cond['userId'] = '';
            $cond['userName'] = '';
        }

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
            $pageNum
        );

        $assignAry = [
            'userId' => $cond['userId'],
            'userName' => $cond['userName'],
            'userList' => $userList
        ];

        return view('manage/adminUser/list', $assignAry);
    }

    /**
     * 検索
     *
     * @param SearchRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function search(SearchRequest $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'serach', $cond);

        return redirect()->route('manageAdminUser');

    }

    public function update(UpdateRequest $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        dump($request->all());
    }


}
