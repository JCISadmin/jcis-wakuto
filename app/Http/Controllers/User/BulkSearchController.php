<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\User\BulkSearch\BulkSearchUploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use App\Models\BulkSearch;
use App\Models\AuthUser;
use App\Models\TMngBatch;
use Illuminate\Support\Facades\Log;
use Datetime;

/**
 * 一括検索画面
 */
class BulkSearchController extends Controller
{
    /**
     * ページ名
     *
     * @var string
     */
    private $searchType = 'normal';

    /**
     * 初期・一覧画面表示
     *
     * @param Request $request
     * @return View|Factory|Application
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

        /**
         * @var AuthUser $user
         */
        $user = auth()->user();

        $model = new BulkSearch();

        $dataList = $model->getList($user->companyId, $pageNum, $this->searchType);
        $assignAry = [
            'dataList' => $dataList,
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
            'pageName' => $this->searchType
        ];

        return view('user/bulkSearch/list', $assignAry);
    }

    /**
     * アップロード画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function add(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $assignAry = [
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
            'pageName' => $this->searchType
        ];

        return view('user/bulkSearch/add', $assignAry);
    }

    /**
     * アップロードアクション
     *
     * @param BulkSearchUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function upload(BulkSearchUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $date = date('Ymd');
        $time = date('his');

        $uploadFile = $request->file('bulk_file');
        $uploadName = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_FILENAME);
        $ext = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_EXTENSION);
        $orgName = 'bulkSearchFile_' . $date . $time;
        $fileName = $orgName . '.' . $ext;

        $filePath = $uploadFile->storeAs('bulkSearch/upload', $fileName);
        $filePath = storage_path('app/' . $filePath);

        $fileType = mime_content_type($filePath);
        if ($fileType === 'text/plain') {
            $fileType = "application/csv";
        }

        if ($fileType !== "application/csv") {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['searchRepFlg'] = $request->input('searchRepFlg');
        $item['retireFlg'] = $request->input('retireFlg');
        $item['filePath'] = $filePath;
        $item['fileType'] = $fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;

        $request->session()->put(__CLASS__ . 'bulkSearch', $item);

        return redirect()->route('userBulkSearchConfirm');
    }

    /**
     * アップロード確認画面表示
     *
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function confirm(Request $request): View|Factory|RedirectResponse|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->session()->get(__CLASS__ . 'bulkSearch');

        $filePath = $item['filePath'];
        $fileType = $item['fileType'];

        $rawCnt = 0;

        if ( $fileType === "application/csv" ) {

            $fp = fopen($filePath, "r");

            $chkType = '';
            while (($data = fgetcsv( $fp )) !== false) {
                if (count($data) != 3) {
                    return back()->withInput()->withErrors(['message' => '無効なファイルフォーマットです。']);
                }

                if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $data[0])) {
                    $data[0] = substr($data[0], 3);
                }

                if ($data[0] != "法人検索" && $data[0] != "個人検索") {
                    return back()->withInput()->withErrors(['message' => '法人検索または個人検索を指定してください。']);

                }

                if ($chkType == '') {
                    $chkType = $data[0];
                } else {
                    if ($chkType !== $data[0]) {
                        return back()->withInput()->withErrors(['message' => '法人検索または個人検索に統一してください。']);
                    }
                }

                $rawCnt++;
            }

            fclose($fp);

            if($rawCnt > 5000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは5000件以内です。']);
            }

        }else{
            throw new Exception('file format error');
        }

        $assignAry = [
            'rawCnt' => $rawCnt,
            'dlFlg' => false,
            'fuzzyFlg' => $item['fuzzyFlg'],
            'searchRepFlg' => $item['searchRepFlg'],
            'retireFlg' => $item['retireFlg'],
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
            'orgName' => $item['orgName'],
            'filePath' => $filePath,
            'fileType' => $item['fileType'],
            'uploadName' =>$item['uploadName'],
            'pageName' => $this->searchType
        ];

        $request->session()->put(__CLASS__ . 'bulkSearch', $assignAry);

        return view('user/bulkSearch/confirm', $assignAry);
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
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->session()->get(__CLASS__ . 'bulkSearch');
        $model = new BulkSearch();
        $mngBatchModel = new TMngBatch();

        /** @var $user AuthUser */
        $user = auth()->user();

        $batchItems['companyId'] = $user->companyId;
        $batchItems['batchId'] = uniqId();
        $contractPlanId = $user->contractPlanId;
        $userId = $user->userId;

        if ($data['fileType'] === "application/csv") {

            $fp = fopen($data['filePath'], 'r');

            while (($line = fgetCsv($fp)) !== false) {

                if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $line[0])) {
                    $line[0] = substr($line[0], 3);
                }

                $cond['cond'][] = [
                    'type' => $line[0],
                    'name' => $line[1],
                    'birthday' => $line[2]
                ];

            }
            $cond['type'] ='CSV';
        }else{
            throw new Exception('file format error');
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

        return redirect()->route('userBulkSearch');
    }

    /**
     * PDF CSVのダウンロード
     *
     * @param $batchId
     * @param $type
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadResult($batchId, $type): BinaryFileResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        /** @var $user AuthUser */
        $user = auth()->user();

        $model = new TMngBatch();
        $mngInfo = $model->get($user->companyId, $batchId);

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
        $filePath = storage_path('app/bulkSearch/download') . '/' . $mngInfo['fileName'] . $ext;

        return response()->download($filePath, $downloadName, $headers);
    }
}
