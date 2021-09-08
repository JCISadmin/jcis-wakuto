<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\TClaim;
use App\Http\Requests\Manage\Claim\SearchRequest;
use Illuminate\Http\RedirectResponse;

/**
 * 管理ユーザー一覧
 */
class ClaimController extends Controller
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

        $assignAry = [
            'claimMonth' => '',
            'companyName' => '',
        ];

        return view('manage/claim/list', $assignAry);
    }

    /**
     * 検索結果表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        
        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new TClaim;
        $claimList = $model->getList($cond['claimMonth'], $cond['companyName'],$pageNum);
        $assignAry = [
            'claimMonth' => $cond['claimMonth'],
            'companyName' => $cond['companyName'],
            'claimList' => $claimList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/claim/list', $assignAry);
    }

    /**
     * 検索アクション
     *
     * @param SearchRequest $request
     * @return RedirectResponse
     */
    public function search(SearchRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);

        return redirect()->route('manageClaimList');
    }

    /**
     * 未請求ボタンをクリック
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     */
    public function claim(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changeClaimStatus($editId, $claimMonth);

        return redirect()->route('manageClaimList');
    }

    /**
     * 未入金ボタンをクリック
     * 
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     */
    public function payment(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $claimMonth = $request->session()->get(__CLASS__ . 'search.claimMonth');
        $model = new TClaim;
        $model->changePaymentStatus($editId, $claimMonth);

        return redirect()->route('manageClaimList');
    }
}
