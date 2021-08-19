<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MAdminUser;

class AdminUserController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request) {

        $model = new MAdminUser();
        $userList = $model->getList('', '', $request->input('pageLine', ''));

        $assignAry = [
            'userList' => $userList
        ];

        return view('manage/adminUser/list', $assignAry);
    }
}
