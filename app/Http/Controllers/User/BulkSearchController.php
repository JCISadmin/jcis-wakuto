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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use App\Models\BulkSearch;
use App\Models\AuthUser;
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

        $pageNum = $request->input('pageLine', '');
        if ($pageNum == '') {
            $pageNum = $request->session()->get(__CLASS__ . 'pageNum');
        } else {
            $request->session()->put(__CLASS__ . 'pageNum', $pageNum);
        }

        $model = new BulkSearch();

        $dataList = $model->getList($pageNum);

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
     * @return Application|Factory|View
     */
    public function confirm(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $item = $request->session()->get(__CLASS__ . 'bulkSearch');

        $filePath = $item['filePath'];
        $fileType = $item['fileType'];

        $rawCnt = 0;

        if( $fileType == "application/csv" ){

            $fp = fopen($filePath, "r");
            while (fgetcsv( $fp )) {
                $rawCnt++;
            }
            fclose($fp);

        }elseif( $fileType == "application/pdf" ){

            $command = sprintf("pdftotext -layout %s" ,$filePath);
            exec($command);

            $fp = fopen($filePath, "r");
            while (fgetcsv( $fp )) {
                $rawCnt++;
            }
            fclose($fp);

        }elseif( $fileType == "application/zip" ){

            for( $i = 0; $i < count($filePath); $i++ ){
                $command = sprintf("pdftotext -layout %s" ,$filePath[$i]);
                exec($command);

                $fp = fopen($filePath[$i], "r");
                while (fgetcsv( $fp )) {
                    $rawCnt++;
                }
                fclose($fp);
            }

        }        

        $isDl = "";

        if( $fileType == "application/pdf" || $fileType == "application/zip" ){

            $isDl = true;
        }

        $assignAry = [
            'rawCnt' => $rawCnt - 1,
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

        if($fileType != "application/csv" && $fileType != "application/pdf" && $fileType != "application/zip"){

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

                for ( $i = 0; $i < $zip->numFiles; $i++ ) {

                    $filePath = storage_path('app/bulkSearch/upload/' . $orgName);
                    $zip->extractTo($filePath);
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
    public function download(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->session()->get(__CLASS__ . 'bulkSearch');

        $model = new BulkSearch();

        $filePath  = $data['filePath'];

        $items = $model->RegistryCSVData($filePath);

        $fileName = $data['orgName'] . '.csv';
        $filePath = storage_path('app/bulkSearch/download/' . $fileName);

        $fp = fopen( $filePath, "w+" );

        foreach ($items as $item) {
            fputcsv($fp, $item);
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

        /** @var $user AuthUser */
        $user = auth()->user();

        $items['companyId'] = $user->companyId;
        $items['batchId'] = uniqId();
        $contractPlanId = auth()->user()->contractPlanId;
        $userId = auth()->user()->userId;

        if( $data['fileType'] == "application/csv"){

            $fileData = file($data['filePath']);

            for($i=0; $i < count($fileData); $i++){

                $cond[$i] = explode(",", $fileData[$i]);
            }


        }elseif( $data['fileType'] == "application/zip" || "application/zip" ){

            $cond = $model->RegistryCSVData($data['filePath']);
        }

        $cond[] = $data['fuzzyFlg'];
        $items['searchCondition'] = json_encode($cond,JSON_UNESCAPED_UNICODE);
        $items['fileName'] = $data['uploadName'];

        
        $model->insData($items);
        dd($cond);

        $command = sprintf("php artisan bulkSearch %s %s %s %s %s" , $items['batchId'], $items['companyId'], $contractPlanId, $userId, $data['fileType']);
        exec($command);

        return redirect()->route('userBulkSearch');
    }


    /**
     * PDFダウンロードアクション
     *
     * @param Request $request
     * @return string
     * @throws Exception
     * */
    public function downloadPDF(Request $request): string
    {
        $model = new BulkSearch();

        /** @var $user AuthUser */
        $user = auth()->user();

        $items['companyId'] = $user->companyId;
        $items['batchId'] = uniqId();

        $fileName = $model->getFileName();

        $stream = $model->makePDF($items['companyId'], $items['batchId'], $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"{$fileName}\"");

        return $stream;
    }
}