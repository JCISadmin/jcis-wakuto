<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MConvertFont;
use App\Models\MConvertFontDetail;
use App\Http\Requests\Manage\ConvertFont\UpdateRequest;


/**
 * 旧字体変換マスタ
 */
class ConvertFontController extends Controller
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

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new MConvertFont();
        $convertFontList = $model->getList($pageNum);

        $assignAry = [
            'convertFontList' => $convertFontList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/convertFont/list', $assignAry);
    }

    public function edit(Request $request): View|Factory|Application
    {        
        $this->actionLog(__CLASS__, __FUNCTION__); 

        $data = $request->all();
        $assignAry = [
            'editItem' => $data['editItem'],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];
        $request->session()->put(__CLASS__ . 'editItem', $data['editItem']);

        return view('manage/convertFont/edit', $assignAry);
    }

    /**
     * 削除
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function delete(Request $request) : RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $delCond = $request->input('delTargetCharacter');
        $MConvertFontModel = new MConvertFont();

        $MConvertFontModel->deleteConvertFont($delCond);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageConvertFont');
    }

    /**
     * 更新
     *
     * @param Request $request
     * @throws Exception
     */
    public function update(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $MConvertFontMmodel = new MConvertFont();
        $MConvertFontDetailMmodel = new MConvertFontDetail();

        $data = $request->all();
        $targetCharacter = $data['updateTargetCharacter'];
        $convertCharacter = $data['updateConvertCharacter'];

        $editItem = $request->session()->get(__CLASS__ . 'editItem');
        if(is_null($editItem)){
            $MConvertFontMmodel->insertConvertFont($targetCharacter, $convertCharacter);
        }else{
            $MConvertFontDetailMmodel->updateConvertFontDetail($targetCharacter, $convertCharacter);
        }

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));
        $assignAry = [
            'editItem' => [
                'editTargetCharacter' => $targetCharacter,
                'editConvertCharacter' => $convertCharacter
            ],
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];
        $request->session()->put(__CLASS__ . 'editItem', $assignAry['editItem']);

        return view('manage/convertFont/edit', $assignAry);
    }

}
