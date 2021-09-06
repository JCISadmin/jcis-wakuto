<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\MUserDetail;

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
     * @throws Exception
     */
    public function login(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $dt = new \DateTime();
        $model = new MUserDetail();

        $ret = Auth::attempt(
            [
                'userId' => $request->post('userId'),
                'password' => $request->post('password'),
                'type' => 0
            ],
            false
        );

        if (!$ret) {
            // 認証失敗
            return redirect()->route('userLogin');
        }

        // TODO 2要素認証を追加

        /** @var AuthUser $user */
        $user = auth()->user();

        if (is_null($user->loginDatetime) == false) {
            // 未ログアウト時も2要素認証とする
            $loginTime = new \DateTime($user->loginDatetime);
            $loginInterval = config('hds.auth.loginInterval');
            $loginTime->add(new \DateInterval($loginInterval));



            if ($loginTime > $dt) {
                // TODO 一時的にログインエラー
                // 2要素認証に変更する
                return back()->withInput()->withErrors(['message' => 'ログアウトされていません。']);
            }

        }

        if ($request->post('remember-me', '') == 'on') {

            $time = time() + 60 * 60 * 24 * 30;
            Cookie::queue('user_userId', $request->post('userId'), $time);
            Cookie::queue('user_pass', $request->post('password'), $time);
        } else {
            Cookie::queue('user_userId', null);
            Cookie::queue('user_pass', null);
        }

        $model->updateLoginTime(
            $user->companyId,
            $user->contractPlanId,
            $user->userId,
            $dt->format('Y-m-d H:i:s')
        );

        return redirect()->route('userHome');

    }

    /**
     * ログアウト
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MUserDetail();

        /** @var AuthUser $user */
        $user = auth()->user();
        $model->updateLoginTime(
            $user->companyId,
            $user->contractPlanId,
            $user->userId,
            null
        );


        Auth::logout();
        return redirect()->route('userLogin');
    }


}
