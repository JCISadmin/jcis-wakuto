<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;

/**
 * お問い合わせ画面
 */
class ContactController extends Controller
{

    /**
     * 初期画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index() {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $assignAry = [
            'selectList' => config('hds.subject'),
        ];

        return view('user/contact/edit', $assignAry);

    }

    /**
     * 確認画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function confirm(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->all();

        $item['name'] = auth()->user()->name;
        $item['mail'] = auth()->user()->mail;
        $item['companyId'] = auth()->user()->companyId;
        $item['departmentJob'] = auth()->user()->departmentJob;

        $assignAry = [
            'item' => $item,
 
        ];

        return view('user/contact/confirm', $assignAry);

    }

        /**
     * 確認画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function send() {
        $this->actionLog(__CLASS__, __FUNCTION__);

        Mail::to('naoki_hagiwara@entrend.net')->send(new Contact(['title' => 'title']));

        return redirect()->route('userHome');
    }






}

