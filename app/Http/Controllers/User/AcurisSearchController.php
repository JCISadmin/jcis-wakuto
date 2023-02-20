<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\AuthUser;
use App\Models\AcurisSearchEngine;
use App\Http\Requests\User\AcurisSearch\SearchRequest;
use App\Http\Requests\User\AcurisSearch\LookupRequest;
use ZipArchive;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\Storage;


/**
 * 海外検索
 */
class AcurisSearchController extends Controller
{

    const TYPE_COMPANY = 'company';
    const TYPE_PERSON = 'person';

    /**
     * 検索についての注意事項表示
     *
     * @param Request $request
     * @return View|Factory|Application
     */
    public function note(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        return view('user/acurisSearch/note');

    }

    /**
     * 初期・検索入力画面表示
     *
     * @param Request $request
     * @return View|Factory|Application
     */
    public function index(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $assignAry = [
            'selectList' => [
                'nationalityList' => config('nationality.acurisSearch.nationalityList'),
            ],
            'unitPrice' => config('hds.acuris.search.normal.unitPrice'),
        ];

        return view('user/acurisSearch/edit', $assignAry);
    }

    /**
     * API 検索
     *
     * @param SearchRequest $request
     * @return RedirectResponse
     */
    public function search(SearchRequest $request): RedirectResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $data = $request->input();

         /** @var AuthUser $user */
        $user = auth()->user();

        $searchModel = new AcurisSearchEngine();

        $keyword = [];
        $result = [];

        // 法人検索
        foreach ($data['companyName'] as $companyName) {
            // 検索文字列が空の場合 スキップ
            if ($companyName === '') {
                continue;
            }

            // 検索実行
            $list = $searchModel->searchCompany($user->companyId, $user->userId, $companyName, $data['datasets'], $data['nationality']);

            // API検索でエラーが発生した場合
            if ($list === FALSE) {
                $keyword[self::TYPE_COMPANY]['error'][$companyName] = $companyName;

            } else {
                // 検索結果配列に追加
                foreach ($list as $value) {
                    if (is_array($value)) {
                        $value['searchType'] = self::TYPE_COMPANY;
                        // 配列を出力用の文字列に変換
                        $value['datasets'] = implode(', ',$value['datasets']);
                        $value['countries'] = implode(', ',$value['countries']);
                        $result[] = $value;
                    }
                }

                // 検索ワード配列に追加
                if (count($list) > 0) {
                    $keyword[self::TYPE_COMPANY]['exist'][$companyName] = $companyName;
                } else {
                    $keyword[self::TYPE_COMPANY]['noExist'][$companyName] = $companyName;
                }
            }
        }

        // 個人検索
        foreach ($data['personName'] as $personName) {

            // 検索文字列が空の場合 スキップ
            if ($personName === '') {
                continue;
            }

            // 検索実行
            $list = $searchModel->searchPerson($user->companyId, $user->userId, $personName, $data['datasets'], $data['nationality'], $data['dob']);

            // API検索でエラーが発生した場合
            if ($list === FALSE) {
                $keyword[self::TYPE_PERSON]['error'][$personName] = $personName;

            } else {
                // 検索結果配列に追加
                foreach ($list as $value) {
                    if (is_array($value)) {
                        $value['searchType'] = self::TYPE_PERSON;
                        // 配列を出力用の文字列に変換
                        $value['datasets'] = implode(', ',$value['datasets']);
                        $value['countries'] = implode(', ',$value['countries']);
                        $value['datesOfBirth'] = implode(', ',$value['datesOfBirth']);
                        $result[] = $value;
                    }
                }

                // 検索ワード配列に追加
                if (count($list) > 0) {
                    $keyword[self::TYPE_PERSON]['exist'][$personName] = $personName;
                } else {
                    $keyword[self::TYPE_PERSON]['noExist'][$personName] = $personName;
                }
            }
        }

        $collection = collect($result);
        $searchData = [
            'keyword' => $keyword,
            'result' => $collection,
            'searchTime' => date("Y/m/d H:i"),
        ];

        $request->session()->put(__CLASS__ . 'searchData', $searchData);
        $request->session()->put(__CLASS__ . 'pageLine', 10);


        return redirect()->route('userAcurisSearchResult');

    }

    /**
     * 検索結果画面表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function result(Request $request): View|Factory|Application 
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $searchDataResult = $searchData['result'];
        $searchDataKeyword = $searchData['keyword'];
        $searchDataSearchTime = $searchData['searchTime'];
        $assignAry = [
            'keyword' => $searchDataKeyword,
            'searchTime' => $searchDataSearchTime,
            'result' => $searchDataResult,
            'unitPrice' => config('hds.acuris.search.detail.unitPrice'),
        ];

        return view('user/acurisSearch/result', $assignAry);
    }

    /**
     * 計算結果 印刷用html表示
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function print(Request $request): View|Factory|Application
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $assignAry = [
            'keyword' => $searchData['keyword'],
            'searchTime' => $searchData['searchTime'],
            'result' => $searchData['result'],
        ];

        return view('user/acurisSearch/resultPrint', $assignAry);
    }

    /**
     * 検索結果PDFの生成
     *
     * @param Request $request
     * @return string
     */
    public function pdf(Request $request): string
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $pdfData = [
            'keyword' => $searchData['keyword'],
            'searchTime' => $searchData['searchTime'],
            'result' => $searchData['result'],
        ];

        $model = new AcurisSearchEngine();
        $fileName = $model->getPdfFileName();
        $string = $model->makePdf($pdfData, $fileName);

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        return $string;
    }

    /**
     * 検索結果EXCELの生成
     *
     * @param Request $request
     * @return bool
     */
    public function excel(Request $request)
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        $searchData = $request->session()->get(__CLASS__ . 'searchData');
        $excelData = [
            'keyword' => $searchData['keyword'],
            'searchTime' => $searchData['searchTime'],
            'result' => $searchData['result'],
        ];

        $model = new AcurisSearchEngine();
        $fileName = $model->getExcelFileName();
        return $model->downloadExcel($excelData, $fileName);
    }

    /**
     * 詳細検索結果PDFを取得
     *
     * @param LookupRequest $request
     * @return BinaryFileResponse
     */
    public function lookupPdf(LookupRequest $request): BinaryFileResponse
    {
        $this->actionLog(__CLASS__, __FUNCTION__);

        /** @var AuthUser $user */
        $user = auth()->user();

        $data = $request->all();

        $model = new AcurisSearchEngine();

        // 一時保存フォルダ生成
        Storage::makeDirectory('acurisSearch/lookup/' .$user->companyId .'/'. $user->userId);

        // 詳細検索実行
        $resourceIds = [];
        $filePathAry = [];
        foreach ($data['resourceId'] as $key => $resourceId) {

            // 法人/個人 API切り替え
            if ($data['searchType'][$key] === self::TYPE_COMPANY) {

                // 法人詳細検索
                $pdfPath = $model->lookupCompany($user->companyId, $user->userId, $resourceId);
            } elseif ($data['searchType'][$key] === self::TYPE_PERSON) {

                // 個人詳細検索
                $pdfPath = $model->lookupPerson($user->companyId, $user->userId, $resourceId);
            }

            // 詳細結果PDFが取得できない場合
            if ($pdfPath === FALSE) {
                // エラーIDとして追加
                $resourceIds[] = [
                    'resourceId' => $resourceId,
                    'name' => $data['name'][$key],
                    'status' => FALSE,
                ];
            } else {
                // 成功IDとして追加
                $resourceIds[] = [
                    'resourceId' => $resourceId,
                    'name' => $data['name'][$key],
                    'status' => TRUE,
                ];
                //出力ファイルに追加
                $filePathAry[] = $pdfPath;
            }
        }

        // 出力ログファイルを作成
        $logFilePath = $model->makeLookupLogFile($user->companyId, $user->userId, $resourceIds);
        $filePathAry[] = $logFilePath;

        // ファイルを ZIPにまとめる
        $zip = new ZipArchive();

        // 一時ファイル(zip)を作成
        $zipName = $model->getZipFileName();

        $zipPath = storage_path('app/acurisSearch/lookup/' .$user->companyId .'/'. $user->userId. '/' .$zipName);

        $zip->open($zipPath, ZipArchive::CREATE);

        // ZIPにファイルを追加
        foreach ($filePathAry as $file) {
            $fileName = basename($file);
            $zip->addFile($file, $fileName);
        }

        $zip->close();

        // PDF一時ファイルを削除
        foreach ($filePathAry as $file) {
            unlink($file);
        }

        // レスポンスヘッダー
        $headers = ['Content-Type' => 'application/zip'];

        return response()->download($zipPath, basename($zipPath), $headers)->deleteFileAfterSend(true);
    }

}
