<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\TInfomation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

/**
 * お知らせコントローラー（ユーザー）
 */
class InfomationController extends Controller
{
    /**
     * 詳細表示
     *
     * @param string $infomationId
     * @return View|Factory|Application
     */
    public function detail(string $infomationId): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new TInfomation();
        $infomation = $model->getPublic($infomationId);

        if (is_null($infomation)) {
            abort(404);
        }

        $assignAry = [
            'infomation' => $infomation,
        ];

        return view('user/infomation/detail', $assignAry);
    }
}