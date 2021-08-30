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
class DataEditController extends Controller
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
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['typeId'] = self::TYPE_CORPORATION;
            $cond['inputName'] = '';
        }

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        if ($cond['typeId'] == self::TYPE_CORPORATION) {
            $model = new MCorporation();
            $editRouteName = 'manageDataEditEditCorporation';
            $deleteRouteName = 'manageDataEditDeleteCorporation';

        } elseif ($cond['typeId'] == self::TYPE_PERSON){
            $model = new MPerson();
            $editRouteName = 'manageDataEditEditPerson';
            $deleteRouteName = 'manageDataEditDeletePerson';

        } else {
            throw new Exception('Invalid Sequence');
        }

        $dataList = $model->getList($cond['inputName'], $pageNum);

        $assignAry = [
            'inputName' => $cond['inputName'],
            'typeId' => $cond['typeId'],
            'editRouteName' => $editRouteName,
            'deleteRouteName' => $deleteRouteName,
            'dataList' => $dataList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/list', $assignAry);

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

        return redirect()->route('manageDataEdit');
    }

    /**
     * 法人編集画面表示
     *
     * @param $editId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function editCorporation($editId, Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MCorporation();

        $item = $model->get($editId);

        $assignAry = [
            'item' => (Array)$item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editCorporation', $assignAry);

    }

    /**
     * 個人編集画面表示
     *
     * @param $editId
     * @param Request $request
     * @return Application|Factory|View
     */
    public function editPerson($editId, Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = new MPerson();

        $item = $model->get($editId);

        $assignAry = [
            'item' => (Array)$item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editPerson', $assignAry);

    }

    /**
     * 法人データ更新アクション
     *
     * @param UpdateCorporationRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function updateCorporation(UpdateCorporationRequest $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MCorporation();
        $model->updateData($data);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        return redirect()->route('manageDataEditEditCorporation', ['editId' => $data['corporationId']]);

    }

    /**
     * 個人データ更新アクション
     *
     * @param UpdatePersonRequest $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function updatePerson(UpdatePersonRequest $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MPerson();
        $model->updateData($data);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        return redirect()->route('manageDataEditEditPerson', ['editId' => $data['personId']]);

    }

    /**
     * 法人データ削除アクション
     *
     * @param $editId
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function deleteCorporation($editId, Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $model = new MCorporation();

        $model->deleteData($editId);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageDataEdit');

    }

    /**
     * 個人データ削除アクション
     *
     * @param $editId
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function deletePerson($editId, Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);
        $model = new MPerson();

        $model->deleteData($editId);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageDataEdit');

    }








}
