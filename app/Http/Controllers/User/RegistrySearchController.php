<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\User\BulkSearch\RegistrySearchUploadRequest;
use App\Http\Requests\User\BulkSearch\RegistrySearchReUploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Models\BulkSearch;
use App\Models\AuthUser;
use ZipArchive;
use App\Models\TMngBatch;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;
use Datetime;

/**
 * 登記簿一括検索画面
 */
class RegistrySearchController extends Controller
{
    /**
     * ページ名
     *
     * @var string
     */
    private $searchType = 'registry';

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
     * @param RegistrySearchUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function upload(RegistrySearchUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
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

        if ($fileType !== "application/csv" && $fileType !== "application/pdf" && $fileType !== "application/zip") {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        if( $fileType === "application/csv" ){
            $fp = fopen($filePath, "r");
            $data = fgetcsv( $fp );
            if (count($data) === 5) {
                //登記簿流用CSV
                $fileType = "registry/csv";
            }else{
                return back()->withInput()->withErrors(['message' => '無効なファイルフォーマットです。']);
            }
        }

        if( $fileType === "application/pdf" ){
            $filePath = [$filePath];
        }

        if( $fileType === "application/zip" ){

            $folders = [];

            $zip = new ZipArchive();

            if ($zip->open($filePath) === true) {

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

                $dir = glob($filePath . '/*');
                foreach($dir as $path){
                    //フォルダが含まれる場合
                    if(is_dir($path)){
                        $files = glob($path.'/*');
                        $folders[] = $files;
                        foreach($files as $file){
                            if(is_file($file)){
                                //アップロードファイル直下に移動
                                rename($file,$filePath.'/'.basename($file));
                            }else{
                                return back()->withInput()->withErrors(['message' => 'ディレクトリ内に対象外のファイルが含まれています。']);
                            }
                        }
                        rmdir($path);
                    }
                }

                //ZIP内にフォルダとファイルが共存する場合
                if(count($folders) > 0 && count($dir) > 1){
                    return back()->withInput()->withErrors(['message' => '無効なディレクトリ構造です。']);
                }

                $filePath = glob($filePath . '/*');
                $zip->close();

            }else{

                return back()->withInput()->withErrors(['message' => 'ZIPファイルを開くことができません。']);
            }

        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['searchRepFlg'] = $request->input('searchRepFlg');
        $item['retireFlg'] = $request->input('retireFlg');
        $item['filePath'] = $filePath;
        $item['fileType'] = $fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;

        $request->session()->put(__CLASS__ . 'registrySearch', $item);

        return redirect()->route('userRegistrySearchConfirm');
    }

    /**
     * 再アップロードアクション
     *
     * @param RegistrySearchReUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function reUpload(RegistrySearchReUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
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

        if( $fileType === "application/csv" ){
            $fp = fopen($filePath, "r");
            $data = fgetcsv( $fp );
            if (count($data) === 5) {
                //登記簿流用CSV
                $fileType = "registry/csv";
            }else{
                return back()->withInput()->withErrors(['message' => '無効なファイルフォーマットです。']);
            }
        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['searchRepFlg'] = $request->input('searchRepFlg');
        $item['retireFlg'] = $request->input('retireFlg');
        $item['filePath'] = $filePath;
        $item['fileType'] = $fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;

        $request->session()->put(__CLASS__ . 'registrySearch', $item);

        return redirect()->route('userRegistrySearchConfirm');
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

        $item = $request->session()->get(__CLASS__ . 'registrySearch');

        $filePath = $item['filePath'];
        $fileType = $item['fileType'];

        $rawCnt = 0;
        $dlFlg = false;
        
        if( $fileType === "registry/csv" ){

            $companyCnt = 0;

            $fp = fopen($filePath, "r");

            $data = fgetcsv( $fp );
            if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $data[1])) {
                $data[1] = substr($data[1], 3);
            }
            if ($data[1] !== '法人検索') {
                return back()->withInput()->withErrors(['message' => '法人情報がありません。']);
            }
            rewind($fp);

            while (($data = fgetcsv( $fp )) !== false) {
                if (count($data) !== 5) {
                    return back()->withInput()->withErrors(['message' => '無効なファイルフォーマットです。']);
                }

                if ($data[1] != "法人検索" && $data[1] != "個人検索") {
                    return back()->withInput()->withErrors(['message' => '法人検索または個人検索を指定してください。']);

                }

                if ($data[1] === '法人検索') {
                    $companyCnt++;
                }

                $rawCnt++;
            }

            fclose($fp);

            if($companyCnt > 10){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータの会社数は10社以内です。']);
            }
 
            if($rawCnt > 1000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは1000件以内です。']);
            }

        } elseif ( $fileType === "application/pdf" || $fileType === "application/zip") {

            $model = new BulkSearch();

            //pdfをtxt化
            foreach($filePath as $file){
                if(mime_content_type($file) === 'application/pdf'){
                    $command = sprintf('pdftotext -layout "%s"',$file);
                    exec($command);
                }else{
                    return back()->withInput()->withErrors(['message' => '対象外のファイルが含まれています。']);
                }
            }
            
            //txtファイルから文字列を抽出
            $registryAry = $model->getRegistryData($filePath, '',$item['searchRepFlg'],$item['retireFlg']);
            if(empty($registryAry)){
                return back()->withInput()->withErrors(['message' => '無効な登記簿です。']);
            }
            foreach($registryAry as $registryData){

                $rawCnt += count($registryData);
            }

            if($rawCnt > 1000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは1000件以内です。']);
            }
            $dlFlg = true;
        }else{
            throw new Exception('file format error');
        }

        $assignAry = [
            'rawCnt' => $rawCnt,
            'dlFlg' => $dlFlg,
            'fuzzyFlg' => $item['fuzzyFlg'],
            'searchRepFlg' => $item['searchRepFlg'],
            'retireFlg' => $item['retireFlg'],
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', ''),
            'orgName' => $item['orgName'],
            'filePath' => $filePath,
            'fileType' => $item['fileType'],
            'uploadName' =>$item['uploadName'],
            'pageName' => $this->searchType,
            'pdf' => storage_path('app/public/myhoken_rule20220320.pdf')
        ];

        $request->session()->put(__CLASS__ . 'registrySearch', $assignAry);

        return view('user/bulkSearch/confirm', $assignAry);
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

        $data = $request->session()->get(__CLASS__ . 'registrySearch');
        $model = new BulkSearch();

        $filePath  = $data['filePath'];

        $uploadName = '';
        if ($data['fileType'] === 'application/pdf') {
            $uploadName = $data['uploadName'];
        }
        $fileItems = $model->getRegistryData($filePath, $uploadName,$data['searchRepFlg'],$data['retireFlg']);
        $fileName = $data['orgName'] . '.csv';
        Storage::makeDirectory('bulkSearch/download');
        $filePath = storage_path('app/bulkSearch/download/' . $fileName);

        $fp = fopen( $filePath, "w+" );
        fwrite($fp, "\xEF\xBB\xBF");

        foreach ($fileItems as $items) {

            foreach ($items as $item) {
                /** @noinspection PhpSwitchCanBeReplacedWithMatchExpressionInspection */
                switch ($item['position']) {
                    case '法人':
                        $csvAry = [
                            pathinfo($item['uploadName'], PATHINFO_FILENAME),
                            $item['type'],
                            $item['companyName'],
                            '\''.mb_convert_kana(str_replace('─','',$item['corporateCode']),"n"),
                            $item['companyAddress'],
                        ];
                        break;
                    default:
                        if(in_array($item['position'],config('hds.registryInfo.position.representative'))){
                            //代表役職
                            $csvAry = [
                                pathinfo($item['uploadName'], PATHINFO_FILENAME),
                                $item['type'],
                                $item['personName'],
                                $item['position'],
                                $item['personAddress'],
                            ];
                            break;
                        }elseif(in_array($item['position'],config('hds.registryInfo.position.normal'))){
                            //代表以外役職
                            $csvAry = [
                                pathinfo($item['uploadName'], PATHINFO_FILENAME),
                                $item['type'],
                                $item['personName'],
                                $item['position'],
                                '',
                            ];
                            break;
                        }else{
                            $csvAry = [];
                        }
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

        $data = $request->session()->get(__CLASS__ . 'registrySearch');
        $model = new BulkSearch();
        $mngBatchModel = new TMngBatch();

        /** @var $user AuthUser */
        $user = auth()->user();

        $batchItems['companyId'] = $user->companyId;
        $batchItems['batchId'] = uniqId();
        $contractPlanId = $user->contractPlanId;
        $userId = $user->userId;

        if ($data['fileType'] === "registry/csv") {

            $fp = fopen($data['filePath'], 'r');

            $fileIdx = -1;
            while (($line = fgetCsv($fp)) !== false) {

                if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $line[0])) {
                    $line[0] = substr($line[0], 3);
                }

                if($line[1] === '法人検索'){
                    $fileIdx++;
                    $cond['cond'][$fileIdx][] = [
                        'fileName' => $line[0],
                        'type' => $line[1],
                        'position' => '法人',
                        'companyName' => $line[2],
                        'corporateCode' => str_replace('\'','',$line[3]),
                        'companyAddress' => $line[4],
                        'uploadName' => $line[0],
                    ];
                }elseif($line[1] === '個人検索'){
                    $cond['cond'][$fileIdx][] = [
                        'fileName' => $line[0],
                        'type' => $line[1],
                        'position' => $line[3],
                        'personName' => $line[2],
                        'personAddress' => $line[4],
                        'uploadName' => $line[0],
                    ];

                }

            }
            $cond['type'] ='CSV';
        
        } elseif ( $data['fileType'] === "application/pdf" ){
            $fileItems = $model->getRegistryData($data['filePath'], $data['uploadName'],$data['searchRepFlg'],$data['retireFlg']);

            foreach($fileItems as $fileIdx => $items){
                foreach($items as $item){
                    switch( $item['position'] ){
                        case '法人':
                            $cond['cond'][$fileIdx][] = [
                                'fileName' => $item['fileName'],
                                'type' => $item['type'],
                                'position' => $item['position'],
                                'companyName' => $item['companyName'],
                                'corporateCode' => mb_convert_kana(str_replace('─','',$item['corporateCode']),"n"),
                                'companyAddress' => $item['companyAddress'],
                                'uploadName' => $item['uploadName'],
                            ];
                            break;
                        default:
                            if(in_array($item['position'],config('hds.registryInfo.position.representative'))){
                                //代表役職
                                $cond['cond'][$fileIdx][] = [
                                    'fileName' => $item['fileName'],
                                    'type' => $item['type'],
                                    'position' => $item['position'],
                                    'personName' => $item['personName'],
                                    'personAddress' => $item['personAddress'],
                                    'uploadName' => $item['uploadName'],
                                ];
                                break;
                            }elseif(in_array($item['position'],config('hds.registryInfo.position.normal'))){
                                //代表以外役職
                                $cond['cond'][$fileIdx][] = [
                                    'fileName' => $item['fileName'],
                                    'type' => $item['type'],
                                    'position' => $item['position'],
                                    'personName' => $item['personName'],
                                    'personAddress' => '',
                                    'uploadName' => $item['uploadName'],
                                ];
                                break;
                            }
                    }
                }
            }
            $cond['type'] ='PDF';
        } elseif ( $data['fileType'] === "application/zip" ){
            $fileItems = $model->getRegistryData($data['filePath'], '',$data['searchRepFlg'],$data['retireFlg']);

            foreach($fileItems as $fileIdx => $items){
                foreach($items as $item){
                    switch( $item['position'] ){
                        case '法人':
                            $cond['cond'][$fileIdx][] = [
                                'fileName' => $item['fileName'],
                                'type' => $item['type'],
                                'position' => $item['position'],
                                'companyName' => $item['companyName'],
                                'corporateCode' => mb_convert_kana(str_replace('─','',$item['corporateCode']),"n"),
                                'companyAddress' => $item['companyAddress'],
                                'uploadName' => $item['uploadName'],
                            ];
                            break;
                        default:
                            if(in_array($item['position'],config('hds.registryInfo.position.representative'))){
                                //代表役職
                                $cond['cond'][$fileIdx][] = [
                                    'fileName' => $item['fileName'],
                                    'type' => $item['type'],
                                    'position' => $item['position'],
                                    'personName' => $item['personName'],
                                    'personAddress' => $item['personAddress'],
                                    'uploadName' => $item['uploadName'],
                                ];
                                break;
                            }elseif(in_array($item['position'],config('hds.registryInfo.position.normal'))){
                                //代表以外役職
                                $cond['cond'][$fileIdx][] = [
                                    'fileName' => $item['fileName'],
                                    'type' => $item['type'],
                                    'position' => $item['position'],
                                    'personName' => $item['personName'],
                                    'personAddress' => '',
                                    'uploadName' => $item['uploadName'],
                                ];
                                break;
                            }
                    }
                }
            }
            $cond['type'] ='ZIP';
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

        return redirect()->route('userRegistrySearch');
    }

}