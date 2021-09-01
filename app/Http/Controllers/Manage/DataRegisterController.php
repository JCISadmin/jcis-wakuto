<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Manage\DataEdit\SearchRequest;
use App\Models\MCorporation;
use App\Models\MPerson;
use App\Http\Requests\Manage\DataEdit\UpdateCorporationRequest;
use App\Http\Requests\Manage\DataEdit\UpdatePersonRequest;


/**
 * データ登録変更画面
 */
class DataRegisterController extends Controller
{

    const TYPE_CORPORATION = 1;
    const TYPE_PERSON = 2;

    /**
     * 初期表示・一覧画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index()
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        return view('manage/dataRegister/add');

    }

}
