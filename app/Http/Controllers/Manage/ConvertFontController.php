<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\MConvertFont;
use App\Models\MConvertFontDetail;
use App\Http\Requests\Manage\ConvertFont\UpdateRequest;
use App\Http\Requests\Manage\ConvertFont\SearchRequest;
use Exception;


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

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['targetCharacter'] = '';
        }

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new MConvertFont();
        $convertFontList = $model->getList(
            $cond['targetCharacter'],
            $pageNum
        );

        $assignAry = [
            'targetCharacter' => $cond['targetCharacter'],
            'convertFontList' => $convertFontList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/convertFont/list', $assignAry);
    }

    /**
     * 編集画面表示
     *
     * @param string $editId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function edit(Request $request, string $editId = ''): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $editId = base64_decode($editId);

        $item = [
            'targetCharacter' => '',
            'convertCharacter' => ''
        ];

        if ($editId != '') {
            $model = new MConvertFontDetail();
            $listObj = $model->get($editId);

            foreach ($listObj as $itemObj) {
                $item['targetCharacter'] = $itemObj->targetCharacter;
                if ($item['convertCharacter'] == '') {
                    $item['convertCharacter'] = $itemObj->convertCharacter;
                } else {
                    $item['convertCharacter'] .= "\n" . $itemObj->convertCharacter;
                }
            }
        }

        $assignAry = [
            'editId' => $editId,
            'item' => $item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/convertFont/edit', $assignAry);
    }

    /**
     * 削除
     *
     * @param Request $request
     * @param $editId
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(Request $request, $editId): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $editId = base64_decode($editId);

        $model = new MConvertFont();
        $model->deleteFont($editId);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageConvertFont');

    }

    /**
     * 更新処理
     *
     * @param UpdateRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function update(UpdateRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();
        $data['convertCharacterAry'] = explode("\r\n", $data['convertCharacter']);

        $model = new MConvertFont();
        $modelDetail = new MConvertFontDetail();

        if ($data['editId'] == '') {
            // 新規
            $model->insertFont($data);
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_INS_SUCCESS'));
            $data['editId'] = $data['targetCharacter'];

        } else {
            // 更新
            $modelDetail->updateFont($data);
            $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        }

        $editId = base64_encode($data['editId']);
        return redirect()->route('manageConvertFontEdit', ['editId' => $editId]);
    }

    /**
     * 検索処理
     * 
     * @param SearchRequest $request
     * @return RedirectResponse
     */
    public function search(SearchRequest $request) {

        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        $request->session()->put(__CLASS__ . 'search', $cond);

        return redirect()->route('manageConvertFont');
    }
}
