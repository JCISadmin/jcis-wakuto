<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\DataRegisterFileCorporation;
use App\Models\DataRegisterFilePerson;
use App\Http\Requests\Manage\DataRegister\UploadRequest;

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

        $assignAry = [
            'errorInfo' => [],
        ];

        return view('manage/dataRegister/add', $assignAry);
    }

    /**
     * アップロードアクション
     *
     * @param UploadRequest $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function upload(UploadRequest $request)
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

        if($header[1] == "法人・団体名(入力用)"){

            $model = new DataRegisterFileCorporation();

        }elseif($header[1] == "氏名(入力用)"){

            $model = new DataRegisterFilePerson();

        }else{

            throw new \Exception("$fileName is invalid header format.");

        }

        try {
            $cntAry = $model->import($filePath);

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['message' => $e->getMessage()]);
        }

        $assignAry = [
            'errorInfo' => $model->errorInfo,
        ];

        if ($cntAry['sucCnt'] > 0) {
            $assignAry['msg'] = __('messages.INF_UPD_SUCCESS')  . '(' . $cntAry['sucCnt'] . '/' .$cntAry['rawCnt'] .')';
        }

        return view('manage/dataRegister/add',$assignAry);
    }

}
