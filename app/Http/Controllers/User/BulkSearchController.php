<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\User\BulkSearch\UploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use App\Models\BulkSearch;
use App\Models\AuthUser;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use App\Models\TMngBatch;
use Illuminate\Support\Facades\Log;
use Datetime;

/**
 * 一括検索画面
 */
class BulkSearchController extends Controller
{

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

        $dataList = $model->getList($user->companyId, $pageNum);
        $assignAry = [
            'dataList' => $dataList,
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', '')
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
            'msg' => $request->session()->get(__CLASS__ . 'msg', '')
        ];

        return view('user/bulkSearch/add', $assignAry);
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
        $isDl = "";

        if ( $fileType == "application/csv" ) {

            $fp = fopen($filePath, "r");

            $bomFlg = false;
            $chkType = '';
            while (($data = fgetcsv( $fp )) !== false) {
                if (count($data) != 3) {
                    return back()->withInput()->withErrors(['message' => 'ファイルフォーマットが違います。']);
                }

                if( $bomFlg === false ){
                    if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $data[0])) {
                        $data[0] = substr($data[0], 3);
                    }
                    $bomFlg = true;
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

        } elseif ( $fileType == "application/pdf" || $fileType == "application/zip") {

            $model = new BulkSearch();
            for ($i = 0; $i < count($filePath); $i++) {
                $command = sprintf("pdftotext -layout %s" ,$filePath[$i]);
                exec($command);

                $workAry = $model->RegistryCSVData([$filePath[$i]], '');
                if(empty($workAry)){
                    return back()->withInput()->withErrors(['message' => '無効な登記簿です。']);
                }
                $rawCnt += count($workAry[0]);

            }

            if($rawCnt > 1000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは1000件以内です。']);
            }
            $isDl = true;
        }

        $assignAry = [
            'rawCnt' => $rawCnt,
            'isDl' => $isDl,
            'fuzzyFlg' => $item['fuzzyFlg'],
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
            'orgName' => $item['orgName'],
            'filePath' => $filePath,
            'fileType' => $item['fileType'],
            'uploadName' =>$item['uploadName'],
        ];

        $request->session()->put(__CLASS__ . 'bulkSearch', $assignAry);

        return view('user/bulkSearch/confirm', $assignAry);
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

        $uploadFile = $request->file('bulk_file');
        $uploadName = pathinfo($uploadFile->getClientOriginalName(),PATHINFO_FILENAME);
        $ext = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_EXTENSION);
        $orgName = 'bulkSearchFile_' . $date . $time;
        $fileName = $orgName . '.' . $ext;

        $filePath = $uploadFile->storeAs('bulkSearch/upload', $fileName);
        $filePath = storage_path('app/' . $filePath);

        $fileType = mime_content_type($filePath);
        if ($fileType == 'text/plain') {
            $fileType = "application/csv";
        }

        if ($fileType != "application/csv" && $fileType != "application/pdf" && $fileType != "application/zip") {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        if( $fileType == "application/pdf" ){
            $filePath = [$filePath];
        }

        if( $fileType == "application/zip" ){

            $zip = new ZipArchive();

            if ($zip->open($filePath) == true) {

                $fileCnt = $zip->numFiles;

                if($fileCnt > 10){

                    return back()->withInput()->withErrors(['message' => 'ZIP内ファイルの上限は10件です。']);
                }

                $filePath = storage_path('app/bulkSearch/upload/' . $orgName);
                $idx = 0;
                while ($zip->statIndex($idx)) {
                    $zipEntry = $zip->statIndex($idx);
                    $rawName = $zip->getNameIndex($idx, ZipArchive::FL_ENC_RAW);
                    $entryName = $zipEntry['name'];
                    $destName = mb_convert_encoding($rawName, 'UTF-8', 'CP932');
                    $zip->renameName($entryName, $destName);
                    $zip->extractTo($filePath, $destName);
                    $zip->renameName($destName, $entryName);
                    $idx++;
                }

                $filePath = glob($filePath . '/*');
                $zip->close();

            }else{

                return back()->withInput()->withErrors(['message' => 'zipファイルを開くことができません。']);
            }

        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['filePath'] =$filePath;
        $item['fileType'] = $fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;


        $request->session()->put(__CLASS__ . 'bulkSearch', $item);

        return redirect()->route('userBulkSearchConfirm');
    }



    /**
     * ダウンロードアクション
     *
     * @param Request $request
     * @return BinaryFileResponse
     * @throws Exception
     * */
    public function download(Request $request): BinaryFileResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->session()->get(__CLASS__ . 'bulkSearch');
        $model = new BulkSearch();

        $filePath  = $data['filePath'];

        $uploadName = '';
        if ($data['fileType'] == 'application/pdf') {
            $uploadName = $data['uploadName'];
        }
        $fileItems = $model->RegistryCSVData($filePath, $uploadName);

        $fileName = $data['orgName'] . '.csv';
        Storage::makeDirectory('bulkSearch/download');
        $filePath = storage_path('app/bulkSearch/download/' . $fileName);

        $fp = fopen( $filePath, "w+" );


        foreach ($fileItems as $items) {

            foreach ($items as $item) {
                /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
                switch ($item['position']) {
                    case '法人名':
                        $csvAry = [
                            pathinfo($item['uploadName'], PATHINFO_FILENAME),
                            $item['type'],
                            $item['companyName'],
                            $item['corporateCode'],
                            $item['companyAddress'],
                        ];
                        break;
                    case '取締役':
                    case '監査役':
                        $csvAry = [
                            pathinfo($item['uploadName'], PATHINFO_FILENAME),
                            $item['type'],
                            $item['position'],
                            $item['personName'],
                            '',
                        ];
                        break;
                    case '代表取締役':
                        $csvAry = [
                            pathinfo($item['uploadName'], PATHINFO_FILENAME),
                            $item['type'],
                            $item['position'],
                            $item['personName'],
                            $item['personAddress'],
                        ];
                        break;
                    default:
                        $csvAry = [];

                }

                fputcsv($fp, $csvAry);
            }
        }
        fclose( $fp );

        return response()->download($filePath, $fileName);
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

        $items['companyId'] = $user->companyId;
        $items['batchId'] = uniqId();
        $contractPlanId = $user->contractPlanId;
        $userId = $user->userId;

        if ($data['fileType'] == "application/csv") {

            $fp = fopen($data['filePath'], 'r');
            while (($line = fgetCsv($fp)) !== false) {

                $cond['cond'][] = [
                    'type' => $line[0],
                    'name' => $line[1],
                    'birthday' => $line[2]
                ];

            }
            $cond['type'] ='CSV';

        } elseif ( $data['fileType'] == "application/pdf" ){
            $cond['cond'] = $model->RegistryCSVData($data['filePath'], $data['uploadName']);
            $cond['type'] ='PDF';

        } else {
            $cond['cond'] = $model->RegistryCSVData($data['filePath'], '');
            $cond['type'] ='PDF';

        }

        $cond['fuzzyFlg'] = $data['fuzzyFlg'];
        $cond['uploadName'] = $data['uploadName'];

        $items['searchCondition'] = json_encode($cond,JSON_UNESCAPED_UNICODE);

        $mngBatchModel->ins($items['companyId'], $items['batchId'], $items['searchCondition']);

        $command = sprintf("/usr/bin/php %s bulkSearch %s %s %s %s %s > /dev/null &" , base_path('artisan')  , $items['batchId'], $items['companyId'], $contractPlanId, $userId, $data['fileType']);
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
        $searchDate = json_decode($mngInfo['searchCondition'], true);

        if ($type == 'pdf') {
            $ext = '.pdf';
            $headers = [['Content-Type' => 'application/pdf']];
            $downloadName = $searchDate['uploadName'] . $ext;
        } else {
            $ext = '.csv';
            $headers = [['Content-Type' => 'application/csv']];
            $dt = new Datetime($mngInfo['createDatetime']);
            $downloadName = $searchDate['uploadName'] . '_' . $dt->format('YmdHis')  .  $ext;
        }
        $filePath = storage_path('app/bulkSearch/download') . '/' . $mngInfo['fileName'] . $ext;

        return response()->download($filePath, $downloadName, $headers);
    }
}
