<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

use App\Models\MAgent;
use App\Models\MAdminUser;

/**
 * 管理ログイン
 */
class LoginController extends Controller
{

    /**
     * 管理者画面初期表示
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
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

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

			/**
 			 *
 			 *
 			 */
			// $agend_cd = "'" . $request->post('userId') . "'"; 
			$userid = $request->post('userId');
			$adminuser = MAdminUser::where('userid', $userid)->first();
			$agentinfo = MAgent::where('agent_cd', $adminuser["agent_cd"])->first();
			$request->session()->put('agentinfo', $agentinfo);
			$request->session()->put('distributor_cd', $agentinfo["distributor_cd"]);
			$request->session()->put('agent_cd', $agentinfo["agent_cd"]);
			$request->session()->put('agent_level', $agentinfo["level"]);
 
			/*
			$agent_cd = $request->post('userId'); 
			
			$agentinfo = MAgent::where('agent_cd', $agent_cd)->first();
			$request->session()->put('agentinfo', $agentinfo);
			$request->session()->put('distributor_cd', $agentinfo["distributor_cd"]);
			$request->session()->put('agent_cd', $agentinfo["agent_cd"]);
			$request->session()->put('agent_level', $agentinfo["level"]);
			*/

            return redirect()->route('manageHome');
        }
        return back()->withInput()->withErrors(['message' => 'ユーザーIDまたはパスワードが違います。']);
        // return redirect()->route('manageLogin');

    }

    /**
     * 管理者ログアウト
     *
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {

        $this->actionLog(__CLASS__, __FUNCTION__);

        Auth::logout();
        return redirect()->route('manageLogin');
    }

}
