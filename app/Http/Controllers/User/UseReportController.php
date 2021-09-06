<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new UseReport();
        $userId = auth()->user()->userId;
        $companyId = auth()->user()->companyId;


        $assignAry = [
            'useReportList' => $model->getList($companyId, $userId),
        ];

        return view('user/useReport/list', $assignAry);
    }

}
