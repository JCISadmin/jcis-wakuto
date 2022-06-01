<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Contracts\View\Factory;
use App\Http\Requests\User\BulkSearch\RegistryBulkSearchUploadRequest;
use App\Http\Requests\User\BulkSearch\RegistryBulkSearchReUploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Models\BulkSearch;
use ZipArchive;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;

/**
 * 登記簿一括検索画面
 */
class RegistryBulkSearchController extends BulkSearchController
{
    /*
     * コンストラクタ
     */
    public function __construct()
    {
        $this->searchType = 'registry';
        $this->route = 'userRegistryBulkSearch';

        parent::__construct();
    }

    /**
     * uploadファイル形式チェック
     *
     * @param $orgName
     */
    public function uploadFileTypeCheck($orgName)
    {
        if ($this->fileType !== "application/csv" && $this->fileType !== "application/pdf" && $this->fileType !== "application/zip") {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        if( $this->fileType === "application/csv" ){
            $fp = fopen($this->filePath, "r");
            $data = fgetcsv( $fp );
            if (count($data) === 5) {
                //登記簿流用CSV
                $this->fileType = "registry/csv";
            }
        }

        if( $this->fileType === "application/pdf" ){
            $this->filePath = [$this->filePath];
        }

        if( $this->fileType === "application/zip" ){

            $folders = [];

            $zip = new ZipArchive();

            if ($zip->open($this->filePath) === true) {

                $fileCnt = $zip->numFiles;

                if($fileCnt > 10){

                    return back()->withInput()->withErrors(['message' => 'ZIP内ファイルの上限は10件です。']);
                }

                $this->filePath = storage_path('app/bulkSearch/upload/' . $orgName);
                $idx = 0;
                while ($zip->statIndex($idx)) {
                    $zipEntry = $zip->statIndex($idx);
                    $rawName = $zip->getNameIndex($idx, ZipArchive::FL_ENC_RAW);
                    $entryName = $zipEntry['name'];
                    $destName = mb_convert_encoding($rawName, 'UTF-8', 'CP932');
                    $zip->renameName($entryName, $destName);
                    $zip->extractTo($this->filePath, $destName);
                    $zip->renameName($destName, $entryName);
                    $idx++;
                }

                $dir = glob($this->filePath . '/*');
                foreach($dir as $path){
                    //フォルダが含まれる場合
                    if(is_dir($path)){
                        $files = glob($path.'/*');
                        $folders[] = $files;
                        foreach($files as $file){
                            if(is_file($file)){
                                //アップロードファイル直下に移動
                                rename($file,$this->filePath.'/'.basename($file));
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

                $this->filePath = glob($this->filePath . '/*');
                $zip->close();

            }else{

                return back()->withInput()->withErrors(['message' => 'ZIPファイルを開くことができません。']);
            }
        }

        return true;
    }

    /**
     * confirmファイルチェック
     *
     * @param $item
     */
    public function confirmFileCheck($item)
    {
        if( $this->fileType === "registry/csv" ){

            $companyCnt = 0;

            $fp = fopen($this->filePath, "r");

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

                $this->rawCnt++;
            }

            fclose($fp);

            if($companyCnt > 10){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータの会社数は10社以内です。']);
            }
 
            if($this->rawCnt > 1000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは1000件以内です。']);
            }

        } elseif ( $this->fileType === "application/pdf" || $this->fileType === "application/zip") {

            $model = new BulkSearch();

            //pdfをtxt化
            foreach($this->filePath as $file){
                if(mime_content_type($file) === 'application/pdf'){
                    $command = sprintf('pdftotext -layout "%s"',$file);
                    exec($command);
                }else{
                    return back()->withInput()->withErrors(['message' => '対象外のファイルが含まれています。']);
                }
            }
            
            //txtファイルから文字列を抽出
            $registryAry = $model->getRegistryData($this->filePath, '',$item['searchRepFlg'],$item['retireFlg']);
            if(empty($registryAry)){
                return back()->withInput()->withErrors(['message' => '無効な登記簿です。']);
            }
            foreach($registryAry as $registryData){

                $this->rawCnt += count($registryData);
            }

            if($this->rawCnt > 1000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは1000件以内です。']);
            }
            $this->dlFlg = true;
        }else{
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        return true;
    }

    /**
     * 検索条件取得
     *
     * @param $data
     */
    public function getSearchCond($data)
    {
        $model = new BulkSearch();

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
            throw new Exception('ファイルフォーマットエラー');
        }

        return $cond;
    }


    /**
     * アップロードアクション
     *
     * @param RegistryBulkSearchUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function uploadRegistry(RegistryBulkSearchUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        return parent::upload($request);
    }


    /**
     * 再アップロードアクション
     *
     * @param RegistryBulkSearchReUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function reUpload(RegistryBulkSearchReUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->actionLog(get_class($this), __FUNCTION__);

        $date = date('Ymd');
        $time = date('his');

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

        if ($this->fileType !== "application/csv") {
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        if( $this->fileType === "application/csv" ){
            $fp = fopen($this->filePath, "r");
            $data = fgetcsv( $fp );
            if (count($data) === 5) {
                //登記簿流用CSV
                $this->fileType = "registry/csv";
            }else{
                return back()->withInput()->withErrors(['message' => '無効なファイルフォーマットです。']);
            }
        }

        $item['fuzzyFlg'] = $request->input('fuzzyFlg');
        $item['searchRepFlg'] = $request->input('searchRepFlg');
        $item['retireFlg'] = $request->input('retireFlg');
        $item['filePath'] = $this->filePath;
        $item['fileType'] = $this->fileType;
        $item['orgName'] = $orgName;
        $item['uploadName'] = $uploadName;

        $request->session()->put(get_class($this) . $this->searchType, $item);

        return redirect()->route('userRegistryBulkSearchConfirm');
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
        $this->actionLog(get_class($this), __FUNCTION__);

        $data = $request->session()->get(get_class($this) . $this->searchType);
        $model = new BulkSearch();

        $this->filePath  = $data['filePath'];

        $uploadName = '';
        if ($data['fileType'] === 'application/pdf') {
            $uploadName = $data['uploadName'];
        }
        $fileItems = $model->getRegistryData($this->filePath, $uploadName,$data['searchRepFlg'],$data['retireFlg']);
        $fileName = $data['orgName'] . '.csv';
        Storage::makeDirectory('bulkSearch/download');
        $this->filePath = storage_path('app/bulkSearch/download/' . $fileName);

        $fp = fopen( $this->filePath, "w+" );
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

        return response()->download($this->filePath, $fileName);
    }



}