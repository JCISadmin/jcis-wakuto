<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

/**
 * 管理ホーム画面
 */
class AdminHomeController extends Controller
{

    /**
     * 初期商事
     *
     * @return View
     */
   public function index(): View
   {
       $this->actionLog(__CLASS__, __FUNCTION__);

        return view('manage/home');
    }


}
