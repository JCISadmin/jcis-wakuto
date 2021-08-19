<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{

    /**
     * 管理者画面初期表示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $ary = [
            'userId' => '',
            'password' => '',
            'rememberMe' => 0
        ];

        if ($request->hasCookie('manage_userId')) {
            if ($request->cookie('manage_userId') != '') {
                $ary = [
                    'userId' => $request->cookie('manage_userId'),
                    'password' => $request->cookie('manage_pass'),
                    'rememberMe' => 1
                ];

            }

        }

        return view('manage/login', $ary);

    }

    /**
     * 管理者ログイン処理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function auth(Request $request) {


        $ret = Auth::attempt(
            [
                'userId' => $request->post('userId'),
                'password' => $request->post('password'),
                'type' => 1
            ],
            false
        );

        if ($ret) {
            if ($request->post('remember-me', '') == 'on') {

                $time = time() + 60 * 60 * 24 * 30;
                Cookie::queue('manage_userId', $request->post('userId'), $time);
                Cookie::queue('manage_pass', $request->post('password'), $time);
            } else {
                Cookie::queue('manage_userId', null);
                Cookie::queue('manage_pass', null);
            }

            // TODO ホーム画面に変更する
            return redirect()->route('manageHome');
        } else {
            return redirect()->route('manageLogin');
        }


    }

    /**
     * 管理者ログアウト
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function logout() {

        $this->actionLog(__CLASS__, __FUNCTION__);

        Auth::logout();
        return view('login', []);
    }

}
