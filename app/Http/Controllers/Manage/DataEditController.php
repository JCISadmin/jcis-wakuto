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
use App\Http\Requests\Manage\DataEdit\UpdateRequest;
use App\Models\MCorporation;
use App\Models\MPerson;

/**
 * データ登録変更画面
 */
class DataEditController extends Controller
{

    /**
     * 初期表示・一覧画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->session()->get(__CLASS__ . 'search');
        if (empty($cond)) {
            $cond['inputName'] = '';
        }

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $mcModel = new MCorporation();

        $dataList = $mcModel->getList(
            $cond['inputName'],
            $pageNum
        );


        $assignAry = [
            'inputName' => $cond['inputName'],
            'dataList' => $dataList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];


        
        return view('manage/dataEdit/list', $assignAry);
    }
    
    /**
     * 検索アクション
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function search(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $cond = $request->all();
        if (empty($cond)) {
            $cond['inputName'] = '';
        }

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }


        $mcModel = new MCorporation();
        $mpModel = new MPerson();

        if($cond['typeId'] == "corporation"){
            $dataList = $mcModel->getList(
                $cond['inputName'],
                $pageNum
            );
    
        }elseif($cond['typeId'] == "person"){
            $dataList = $mpModel->getList(
                $cond['inputName'],
                $pageNum
            );

        }

        $assignAry = [
            'inputName' => $cond['inputName'],
            'dataList' => $dataList,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];


        return view('manage/dataEdit/list', $assignAry);

    }

    /**
     * 法人編集画面表示
     *
     * @param $tariff_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCorporation($editId, Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $mcModel = new MCorporation();

        $item = $mcModel->get($editId);


        $assignAry = [
            'item' => $item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editCorporation', $assignAry);

    }

        /**
     * 個人編集画面表示
     *
     * @param $tariff_id
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPerson($editId, Request $request) {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $mpModel = new MPerson();

        $item = $mpModel->get($editId);

        $assignAry = [
            'item' => $item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editPerson', $assignAry);

    }




    /**
     * 法人データ更新アクション
     *
     * @param Request $request
     * @throws Exception
     */
    public function updateCorporation(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();
        

        $model = new MCorporation();

        $model->updateData($data);



        $item = $model->get($data['corporationId']);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        

        $assignAry = [
            'item' =>$item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editCorporation', $assignAry);
    }

    /**
     * 個人データ更新アクション
     *
     * @param Request $request
     * @throws Exception
     */
    public function updatePerson(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MPerson();

        $model->updateData($data);



        $item = $model->get($data['personId']);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_UPD_SUCCESS'));

        

        $assignAry = [
            'item' =>$item,
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
        ];

        return view('manage/dataEdit/editPerson', $assignAry);
    }



    /**
     * 法人データ削除アクション
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function deleteCorporation(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MCorporation();

        $model->deleteData($data);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageDataEdit');

    }

    /**
     * 個人データ削除アクション
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function deletePerson(Request $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->all();

        $model = new MPerson();

        $model->deleteData($data);

        $request->session()->flash(__CLASS__ . 'msg', __('messages.INF_DEL_SUCCESS'));

        return redirect()->route('manageDataEdit');

    }








}
