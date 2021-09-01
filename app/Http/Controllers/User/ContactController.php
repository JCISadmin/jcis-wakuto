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
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;
use App\Models\MUserCompany;

/**
 * お問い合わせ画面
 */
class ContactController extends Controller
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

        $ssData = $request->session()->get(__CLASS__ . 'contact');

        
        if($ssData == null){
            $ssData = [
                'subject' =>'',
                'contactDetail' =>'',
            ];
        }
        
        $assignAry = [
            'ssData' => $ssData,
            'selectList' => config('hds.contact.subject'),
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
    public function confirm(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->all();


        $request->session()->flash(__CLASS__ . 'contact', $item);

        /** @var $user AuthUser */
        $user = auth()->user();

        $model = new MUserCompany();

        $userCompany = $model->get($user->companyId);

        $item['subject'] = config('hds.contact.subject.subject_' . $item['subject']);

        $item['name'] = $user->name;
        $item['mail'] = $user->mail;
        $item['companyName'] = $userCompany['userCompany']['name'];
        $item['departmentJob'] = $user->departmentJob;

        $request->session()->put(__CLASS__ . 'confirm', $item);

        $assignAry = [
            'item' => $item,

        ];

        return view('user/contact/confirm', $assignAry);

    }

    /**
     * メール送信
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function send(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->session()->get(__CLASS__ . 'confirm');

        $mailTo = config('hds.contact.to');
        Mail::to($mailTo)->send(new Contact($item));

        return redirect()->route('userHome');
    }






}

