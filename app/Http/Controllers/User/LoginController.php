<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AuthUser;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\MUserDetail;
use App\Models\T2FactToken;
use App\Models\T2FactMng;
use App\Mail\AuthCode;
use Illuminate\Support\Facades\Mail;

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

        $dt = new DateTime();
        $model = new MUserDetail();
        $tokenModel = new T2FactToken();
        $tokenMngModel = new T2FactMng();

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
            // return redirect()->route('userLogin');
            return back()->withInput()->withErrors(['message' => 'ユーザーIDまたはパスワードが違います。']);
        }

        /** @var AuthUser $user */
        $user = auth()->user();

        if (is_null($user->loginDatetime) == false) {
            // 未ログアウト時処理
            $loginTime = new DateTime($user->loginDatetime);
            $loginInterval = config('hds.auth.loginInterval');
            $loginTime->add(new DateInterval($loginInterval));

            if ($loginTime > $dt) {
                return back()->withInput()->withErrors(['message' => '多重ログイン状態です。']);
            }

        }

        // 2要素認証チェック
        $ret = $tokenMngModel->checkClient(
            $user->companyId,
            $user->contractPlanId,
            $user->userId,
            $request->ip()
        );

        if ($ret == false) {
            $tokenAry = $tokenModel->createToken($user->userId);
            Mail::to($user->mail)->send(new AuthCode($tokenAry));

            return redirect()->route('userLoginAuth', ['tokenId' => $tokenAry['tokenId']]);
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

        /** @var AuthUser $user */
        $user = auth()->user();

        if(is_null($user) === false){
            $model = new MUserDetail();

            $model->updateLoginTime(
                $user->companyId,
                $user->contractPlanId,
                $user->userId,
                null
            );

            Auth::logout();
        }

        return redirect()->route('userLogin');
    }

    /**
     * 2要素認証画面
     *
     * @param string $tokenId
     * @return Application|Factory|View|RedirectResponse
     */
    public function authCode(string $tokenId = ''): View|Factory|RedirectResponse|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        if ($tokenId == '') {
            return redirect()->route('userLogin');
        }

        return view('user/loginAuth', ['tokenId' => $tokenId]);

    }


    /**
     * 認証コードチェック
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authCodeCheck(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();
        if (isset($data['tokenId']) == false) {
            return redirect()->route('userLogin');
        }

        if (isset($data['authCode']) == false) {
            return redirect()->route('userLogin');
        }

        $model = new T2FactToken();
        $ret = $model->authCodeCheck($data['tokenId'], $data['authCode']);

        if ($ret == false) {
            return back()->withInput()->withErrors(['message' => '認証に失敗しました。']);
        }

        /** @var AuthUser $user */
        $user = auth()->user();

        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');

        // 2要素管理情報の登録
        $factMngModel = new T2FactMng();
        $factMngModel->addClient(
            $user->companyId,
            $user->contractPlanId,
            $user->userId,
            $ip,
            $userAgent
        );

        return redirect()->route('userHome');

    }



}
