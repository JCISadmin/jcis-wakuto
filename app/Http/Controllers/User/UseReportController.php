<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UseReport;

/**
 * 利用明細画面
 */
class UseReportController extends Controller
{

    /**
     * 初期画面表示
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;
        

        $assignAry = [
            'useReportList' => $model->getList($companyId, $userId),
        ];

        return view('user/useReport/list', $assignAry);
    }

}
