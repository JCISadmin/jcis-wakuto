<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\DataRegisterFileCorporation;
use App\Models\DataRegisterFilePerson;
use App\Http\Requests\Manage\DataRegister\UploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\VaildException;

/**
 * データ登録変更画面
 */
class DataRegisterController extends Controller
{

    /**
     * 初期表示・一覧画面表示
     *
     * @param Request $request
     * @return View|Factory|Application
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $assignAry = [
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', '')
        ];

        return view('manage/dataRegister/add', $assignAry);
    }

    /**
     * アップロードアクション
     *
     * @param UploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function upload(UploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $date = date('Ymd');
        $time = date('his');


        $uploadFile = $request->file('csv_file');
        $fileName = 'regdatafile_' . $date . $time;
        $filePath = $uploadFile->storeAs('dataRegister', $fileName);
        $filePath = storage_path('app/' . $filePath);

        $fp = fopen($filePath, "r");
        $header = fgetcsv($fp, 0);
        fclose($fp);

        if ($header[1] == "法人・団体名(入力用)") {
            $model = new DataRegisterFileCorporation();

        } elseif($header[1] == "氏名(入力用)") {
            $model = new DataRegisterFilePerson();

        } else {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);

        }

        try {
            $cntAry = $model->import($filePath);

        } catch (VaildException $e) {
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

        $request->session()->flash(__CLASS__ . 'errorInfo', $model->errorInfo);

        if($cntAry['rawCnt'] >= 5000){
            return back()->withInput()->withErrors(['message' => '5001件以降のデータは更新できません。']);
        }

        if ($cntAry['sucCnt'] > 0) {
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS')  . '(' . $cntAry['sucCnt'] . '/' .$cntAry['rawCnt'] .')');
        }

        return redirect()->route('manageDataRegister');

    }

}
