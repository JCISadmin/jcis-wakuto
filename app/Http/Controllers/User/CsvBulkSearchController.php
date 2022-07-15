<?php /** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Http\Controllers\User;

use App\Http\Requests\User\BulkSearch\CsvBulkSearchUploadRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Exception;
use Throwable;


/**
 * 一括検索画面
 */
class CsvBulkSearchController extends BulkSearchController
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->searchType = 'normal';
        $this->route = 'userCsvBulkSearch';
    }

    /**
     * uploadファイル形式チェック
     */
    public function uploadFileTypeCheck()
    {
        if ($this->fileType !== "application/csv"){
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }
        
        return true;
    }

    /**
     * confirmファイルチェック
     */
    public function confirmFileCheck()
    {
        if ( $this->fileType === "application/csv" ) {

            $fp = fopen($this->filePath, "r");

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

                $this->rawCnt++;
            }

            fclose($fp);

            if($this->rawCnt > 5000){
                return back()->withInput()->withErrors(['message' => 'アップロード可能なデータは5000件以内です。']);
            }

        }else{
            return back()->withInput()->withErrors(['message' => 'ファイル形式が違います。']);
        }

        return true;
    }

    /**
     * 検索条件取得
     *
     * @param $data
     * @return $cond
     */
    public function getSearchCond($data)
    {
        if ($data['fileType'] === "application/csv") {

            $fp = fopen($data['filePath'], 'r');

            while (($line = fgetCsv($fp)) !== false) {

                if (preg_match('/^[\x0x\xef][\x0x\xbb][\x0x\xbf]/', $line[0])) {
                    $line[0] = substr($line[0], 3);
                }

                $cond['cond'][] = [
                    'type' => $line[0],
                    'name' => $line[1],
                    'birthday' =>  trim($line[2]),
                ];

            }
            $cond['type'] ='CSV';
        }else{
            throw new Exception('ファイルフォーマットエラー');
        }

        return $cond;
    }

    /**
     * アップロードアクション
     *
     * @param CsvBulkSearchUploadRequest $request
     * @return Factory|RedirectResponse|\Illuminate\View\View
     * @throws Throwable
     */
    public function uploadCsv(CsvBulkSearchUploadRequest $request): Factory|\Illuminate\View\View|RedirectResponse
    {
        return parent::upload($request);
    }

}
