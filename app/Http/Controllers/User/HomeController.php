<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;


/**
 * ユーザー向けホーム画面
 *
 */
class HomeController extends Controller
{

    /**
     * 初期表示
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        return view('user/home');
    }

}
