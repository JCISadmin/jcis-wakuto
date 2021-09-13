<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Http\Requests\User\BulkSearch\UploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;
use App\Exceptions\VaildException;
use ZipArchive;

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

        $assignAry = [
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
     * @return Application|Factory|View
     */
    public function confirm(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->session()->get(__CLASS__ . 'bulkSearch');
        
        $filePath = $item['filePath'];
        $fileType = $item['fileType'];

        if( $fileType == "text/plain" ){

            $fp = fopen($filePath, "r");
            for( $rawCnt = 0; fgetcsv( $fp ); $rawCnt++ );
            fclose($fp);

        }elseif( $fileType == "application/pdf" ){

            $command = sprintf("pdftotext %s" ,$filePath);
            exec($command);

            $fp = fopen($filePath, "r");
            for( $rawCnt = 0; fgets( $fp ); $rawCnt++ );
            fclose($fp);
            
        }elseif( $fileType == "application/zip" ){

            $rawCnt = 0;
            
            for( $i=0; $i < count($filePath); $i++ ){
                
                $command = sprintf("pdftotext %s" ,$filePath[$i]);
                exec($command);

                $fp = fopen($filePath[$i], "r");
                for( $rawCnt; fgets( $fp ); $rawCnt++ );
                fclose($fp);
            }
            
        }

        $isDl = "";

        if( $fileType == "application/pdf" || $fileType == "application/zip" ){

            $isDl = true;
        }

        $assignAry = [
            'rawCnt' => $rawCnt-1,
            'isDl' => $isDl,
            'aimai' => $item['isAimai'],
            'errorInfo' => $request->session()->get(__CLASS__ . 'errorInfo', []),
            'msg' => $request->session()->get(__CLASS__ . 'msg', '')
        ];

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
        $ext = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_EXTENSION);
        $orgName = 'bulkSearchFile_' . $date . $time;
        $fileName = $orgName . '.' . $ext;
        
        $filePath = $uploadFile->storeAs('bulkSearch', $fileName);
        $filePath = storage_path('app/' . $filePath);
        
        $fileType = mime_content_type($filePath);
        
        if($fileType != "text/plain" && $fileType != "application/pdf" && $fileType != "application/zip"){
            
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        if( $fileType == "application/zip" ){

            $zip = new ZipArchive();

            if ($zip->open($filePath) == true) {
                
                $fileCnt = $zip->numFiles;
                
                if($fileCnt > 10){
                    
                    return back()->withInput()->withErrors(['message' => 'ZIP内ファイルの上限は10件です。']);
                }
                
                mkdir(storage_path('/app/bulkSearch/' . $orgName));
                
                for ( $i = 0; $i < $zip->numFiles; $i++ ) {

                    $filePath = storage_path('app/bulkSearch/' . $orgName);
                    $zip->extractTo($filePath);
                }
                
                $filePath = glob($filePath . '/*');
                $zip->close();

            }else{

                return back()->withInput()->withErrors(['message' => 'zipファイルを開くことができません。']);
            }

        }

        $item['isAimai'] = $request->aimai;
        $item['filePath'] =$filePath;
        $item['fileType'] = $fileType;

        $request->session()->put(__CLASS__ . 'bulkSearch', $item);

        return redirect()->route('userBulkSearchConfirm');
    }



    /**
     * ダウンロードアクション
     *
     * @param DownloadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function download(DownloadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $model = BulkSearch();

        
        $filePath = Storage::path('private/profile.png');

        $fileName = 'profile.png';

        $mimeType = Storage::mimeType('private/profile.png');

        $headers = [['Content-Type' => $mimeType]];

        return response()->download($filePath, $fileName, $headers);



    }










}