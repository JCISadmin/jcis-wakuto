<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use Throwable;
use App\Models\BulkSearch;
use App\Models\AuthUser;
use App\Models\TMngBatch;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Datetime;

/**
 * 一括検索
 */
class BulkSearchController extends Controller
{

    protected string $searchType;
    protected string $route;

    protected $filePath;
    protected string $fileType;
    protected int $rawCnt;
    protected bool $dlFlg;

    /**
     * 初期・一覧画面表示
     *
     * @param Request $request
     * @return View|Factory|Application
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(get_class($this) . 'pageNum');
        } else {
            $request->session()->put(get_class($this) . 'pageNum', $pageNum);
        }

        /**
         * @var AuthUser $user
         */
        $user = auth()->user();

        $model = new BulkSearch();

        $dataList = $model->getList($user->companyId, $pageNum, $this->searchType);
        $assignAry = [
            'dataList' => $dataList,
            'errorInfo' => $request->session()->get(get_class($this) . 'errorInfo', []),
            'msg' => $request->session()->get(get_class($this) . 'msg', ''),
        ];

        return view('user/bulkSearch/'.$this->searchType.'List', $assignAry);
    }

    /**
     * アップロード画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function add(Request $request): View|Factory|Application
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $assignAry = [
            'errorInfo' => $request->session()->get(get_class($this) . 'errorInfo', []),
            'msg' => $request->session()->get(get_class($this) . 'msg', ''),
            'searchType' => $this->searchType
        ];

        return view('user/bulkSearch/'.$this->searchType.'Add', $assignAry);
    }
    
    /**
     * ダウンロード削除
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function downloadDelete(Request $request): RedirectResponse
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        /** @var $user AuthUser */
        $user = auth()->user();

        $mngBatchModel = new TMngBatch();

        // ダウンロード削除対象 batchId
        $delBatchIds = $request->input('delBatchId');

        foreach ($delBatchIds as $delBatchId) {

            $mngBatchModel->softDelete($user->companyId, $delBatchId);
        }

        return redirect()->route($this->route);
    }

    /**
     * アップロードアクション
     *
     * @param Request $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function upload(Request $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $date = date('Ymd');
        $time = date('His');

        $uploadFile = $request->file('bulk_file');
        $uploadName = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_FILENAME);
        $ext = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_EXTENSION);
        $orgName = 'bulkSearchFile_' . $date . $time;
        $fileName = $orgName . '.' . $ext;

        $this->filePath = $uploadFile->storeAs('bulkSearch/upload', $fileName);
        $this->filePath = storage_path('app/' . $this->filePath);

        $this->fileType = mime_content_type($this->filePath);
        if ($this->fileType === 'text/plain') {
            $this->fileType = "application/csv";
        }

        $result = $this->uploadFileTypeCheck($orgName);

        if($result !== true){
            return $result;
        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['searchRepFlg'] = $request->input('searchRepFlg');
        $item['retireFlg'] = $request->input('retireFlg');
        $item['filePath'] = $this->filePath;
        $item['fileType'] = $this->fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;

        $request->session()->put(get_class($this) . $this->searchType, $item);

        return redirect()->route($this->route.'Confirm');
    }

    /**
     * アップロード確認画面表示
     *
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function confirm(Request $request): View|Factory|RedirectResponse|Application
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $item = $request->session()->get(get_class($this) . $this->searchType);

        $this->filePath = $item['filePath'];
        $this->fileType = $item['fileType'];

        $this->rawCnt = 0;
        $this->dlFlg = false;

        $result = $this->confirmFileCheck($item);

        if($result !== true){
            return $result;
        }

        $assignAry = [
            'rawCnt' => $this->rawCnt,
            'dlFlg' => $this->dlFlg,
            'fuzzyFlg' => $item['fuzzyFlg'],
            'searchRepFlg' => $item['searchRepFlg'],
            'retireFlg' => $item['retireFlg'],
            'errorInfo' => $request->session()->get(get_class($this) . 'errorInfo', []),
            'msg' => $request->session()->get(get_class($this) . 'msg', ''),
            'orgName' => $item['orgName'],
            'filePath' => $this->filePath,
            'fileType' => $item['fileType'],
            'uploadName' =>$item['uploadName'],
        ];

        $request->session()->put(get_class($this) . $this->searchType, $assignAry);

        return view('user/bulkSearch/'.$this->searchType.'Confirm', $assignAry);
    }

    /**
     * 一括検索アクション
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     * */
    public function bulkSearch(Request $request): RedirectResponse
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $data = $request->session()->get(get_class($this) . $this->searchType);
        $mngBatchModel = new TMngBatch();

        /** @var $user AuthUser */
        $user = auth()->user();

        $batchItems['companyId'] = $user->companyId;
        $batchItems['batchId'] = uniqId();
        $contractPlanId = $user->contractPlanId;
        $userId = $user->userId;

        $cond = $this->getSearchCond($data);

        if(is_null($cond)){
            throw new Exception('データが含まれていません。');
        }
        
        $cond['fuzzyFlg'] = $data['fuzzyFlg'];
        $cond['uploadName'] = $data['uploadName'];

        $jsonData = json_encode($cond,JSON_UNESCAPED_UNICODE);
        if (!file_exists(storage_path('app/bulkSearch/seachCond'))) {
            mkdir(storage_path('app/bulkSearch/seachCond'));
        }
        if (file_exists(storage_path('app/bulkSearch/seachCond') . '/'. $data['uploadName'].'.json')) {
        }

        $jsonPath = storage_path('app/bulkSearch/seachCond') . '/'. $data['orgName'].'_' . $batchItems['batchId']. '.json';
        file_put_contents($jsonPath,$jsonData);

        $mngBatchModel->ins($batchItems['companyId'], $batchItems['batchId'], $this->searchType, $jsonPath);

        $command = sprintf("/usr/bin/php %s bulkSearch %s %s %s %s %s > /dev/null &" , base_path('artisan')  , $batchItems['batchId'], $batchItems['companyId'], $contractPlanId, $userId, $data['fileType']);
        Log::info('BULK SEARCH CMD:' . $command);
        exec($command);

        return redirect()->route($this->route);
    }

    /**
     * 検索結果PDF/CSVのダウンロード
     *
     * @param $batchId
     * @param $type
     * @return BinaryFileResponse|RedirectResponse
     * @throws Exception
     */
    public function downloadResult($batchId, $type): BinaryFileResponse|RedirectResponse
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        /** @var $user AuthUser */
        $user = auth()->user();

        $model = new TMngBatch();
        $mngInfo = $model->get($user->companyId, $batchId);

        // ダウンロード無効データの場合
        if($mngInfo['delFlg'] == TRUE){
            return back()->withInput()->withErrors(['message' => 'このファイルはダウンロードが許可されていません。']);
        }

        if(file_exists($mngInfo['searchCondition']) === false ){
            $searchData = json_decode($mngInfo['searchCondition'] , true);
        }else{
            $jsonData = file_get_contents($mngInfo['searchCondition']);
            if (!$jsonData) {
                throw new Exception('file_get_contents() Error');
            }
            $jsonData = mb_convert_encoding($jsonData, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            $searchData = json_decode($jsonData , true);            
        }

        if ($type === 'pdf') {
            //PDFボタン押下時
            $ext = '.zip';
            $headers = [['Content-Type' => 'application/zip']];

            if (file_exists(storage_path('app/bulkSearch/download/' . $mngInfo['fileName'] . '.pdf'))) {
                $ext = '.pdf';
                $headers = [['Content-Type' => 'application/pdf']];
            }

            $downloadName = $searchData['uploadName'] . $ext;
        } else {
            //CSVボタン押下時
            $ext = '.csv';
            $headers = [['Content-Type' => 'application/csv']];
            $dt = new Datetime($mngInfo['createDatetime']);
            $downloadName = $searchData['uploadName'] . '_' . $dt->format('YmdHis')  .  $ext;
        }
        $this->filePath = storage_path('app/bulkSearch/download') . '/' . $mngInfo['fileName'] . $ext;

        return response()->download($this->filePath, $downloadName, $headers);
    }


}
