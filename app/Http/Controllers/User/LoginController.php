<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

/**
 * ユーザーログイン
 */
class LoginController extends Controller
{

    /**
     * 初期表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $ary = [
            'userId' => '',
            'password' => '',
            'rememberMe' => 0
        ];

        if ($request->hasCookie('user_userId')) {
            if ($request->cookie('user_userId') != '') {
                $ary = [
                    'userId' => $request->cookie('user_userId'),
                    'password' => $request->cookie('user_pass'),
                    'rememberMe' => 1
                ];
            }
        }


        return view('user/login', $ary);
    }


    /**
     * ログイン処理
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $ret = Auth::attempt(
            [
                'userId' => $request->post('userId'),
                'password' => $request->post('password'),
                'type' => 0
            ],
            false
        );

        // TODO 2要素認証を追加


        if ($ret) {
            if ($request->post('remember-me', '') == 'on') {

                $time = time() + 60 * 60 * 24 * 30;
                Cookie::queue('user_userId', $request->post('userId'), $time);
                Cookie::queue('user_pass', $request->post('password'), $time);
            } else {
                Cookie::queue('user_userId', null);
                Cookie::queue('user_pass', null);
            }

            return redirect()->route('userHome');
        }

        return redirect()->route('userLogin');

    }

    /**
     * ログアウト
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {

        $this->actionLog(__CLASS__, __FUNCTION__);

        Auth::logout();
        return redirect()->route('userLogin');
    }


}
