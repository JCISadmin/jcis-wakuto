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
        $lists = $model->getList('', '');

        $assingAry = [
            'lists' => $lists
        ];

        return view('manage/adminUser/list', $assingAry);
    }
}
