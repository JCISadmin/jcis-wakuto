<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

/** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Models;

use Exception;
use Datetime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use TCPDF;

/**
 * Class DataRegister
 *   ファイルインポート
 *
 * @package App\Models
 */
class BulkSearch extends BaseModel
{
    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //


    private array $errorMsg = [];

    public array $errorInfo;

    use HasFactory;

    /**
     * テーブル名
     *
     * @var string
     */
    protected $table = 'tMngBatch';



    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * ファイル取込処理
     *
     */
    public function import()
    {
        $this->errorInfo = array();
    }


    // ---------------------------------------------------------------- //
    // ----------------------- Methods protected ---------------------- //
    // ---------------------------------------------------------------- //

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Private ------------------------ //
    // ---------------------------------------------------------------- //

    /**
     * 一覧取得
     *
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($pageLine): LengthAwarePaginator
    {

        $query = DB::table($this->table);

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        return $query->paginate($pageLine);
    }

    /**
     * 登記簿情報からCSV配列を作成
     *
     * @param $filePath
     * @return array
     */
    public function RegistryCSVData($filePath): array
    {
        $registry = [];
        $csvData = [];

        for($i = 0 ; $i < count($filePath); $i++){

            $filePath[$i] = substr($filePath[$i], 0, -3) . 'txt';

            $txtFileName[$i] = basename($filePath[$i]);

            $contents = file($filePath[$i]);

            for($j = 0; $j < count($contents); $j++){

                if ( strpos( $contents[$j], "会社法人等番号" ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '会社法人等番号'=>'',
                        '－'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);
                    $registry['corporateCode'] = mb_convert_kana(str_replace($keys,$values,$contents[$j]), "n");
                }

                if ( strpos( $contents[$j], "商 号" ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '商号'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);
                    $registry['companyName'] = str_replace($keys,$values,$contents[$j]);
                }

                if ( strpos( $contents[$j], "本 店" ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '本店'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 15) );

                    $str = str_replace($keys,$values,$str);

                    $registry['companyAddress'] = $str;
                }

                if ( strpos( $contents[$j], " 代表取締役 " ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '代表取締役'=>'',
                        '├'=>'',
                        '┨'=>'',
                        '－'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '├', 8) );

                    if($str == null){

                        $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 12) );
                    }

                    $str = str_replace($keys,$values,$str);


                    $registry['CEOName'][] = $str;
                }

                if ( strpos( $contents[$j], " 代表取締役 " ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j-1], 0, mb_strpos($contents[$j-1], '│', 8) );
                    if($str == null){

                        $str = mb_substr($contents[$j-1], 0, mb_strpos($contents[$j-1], '├', 8) );
                    }

                    $str = str_replace($keys,$values,$str);

                    $registry['CEOAddress'][]  = $str;
                }

                if ( strpos( $contents[$j], " 取締役 " ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '取締役'=>'',
                        '├'=>'',
                        '┨'=>'',
                        '－'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 15) );

                    $str = mb_substr($str, mb_strpos($contents[$j], '取締役'));
                    $str = str_replace($keys,$values,$str);

                    $registry['directorName'][] = $str;
                }

                if ( strpos( $contents[$j], " 監査役 " ) ) {

                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '監査役'=>'',
                        '├'=>'',
                        '┨'=>'',
                        '－'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 15) );

                    $str = str_replace($keys,$values,$str);

                    $registry['auditorName'][] = $str;
                }
            }

            $csvData[] = [
                'fileName' => $txtFileName[$i],
                'type' => '法人名',
                'position' => '法人名',
                'companyName' => $registry['companyName'],
                'corporateCode' => $registry['corporateCode'],
                'companyAddress' => $registry['companyAddress'],
            ];


            if(isset($registry['directorName'])){

                foreach($registry['directorName'] as $directorName){

                    $csvData[] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '取締役',
                        'personName' => $directorName,
                        'personAddress' => '',
                    ];
                }
            }


            if(isset($registry['CEOName'])){

                foreach($registry['CEOName'] as $key => $CEOName){

                    $csvData[] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '代表取締役',
                        'personName' => $CEOName,
                        'personAddress' => $registry['CEOAddress'][$key]
                    ];
                }
            }

            if(isset($registry['auditorName'])){

                foreach($registry['auditorName'] as $auditorName){

                    $csvData[] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '監査役',
                        'personName' => $auditorName,
                        'personAddress' => '',
                    ];
                }
            }

        }

        return $csvData;
    }

    /**
     * テーブル検索
     *
     * @param $cond
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $fileType
     * @return array
     * @throws Exception
     */
    public function search($cond, $companyId, $contractPlanId, $userId, $fileType): array
    {
        $model = new SearchEngine();

        $isFuzzy = '';
        $corporationList = [];
        $personList = [];

        if ($cond['fuzzyFlg'] == 'on') {
            $isFuzzy = true;
        }

        if ( $fileType == "application/csv" ) {

            foreach ($cond['cond'] as $item) {
                if ($item['type'] === '法人検索') {
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $item['name'], '', $isFuzzy);

                } elseif ($item['type'] === '個人検索') {
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $item['name'], '', '', $isFuzzy, $item['birthday']);

                }
            }

        } elseif ( $fileType == "application/pdf" ) {

            foreach ($cond['cond'] as $item) {
                if ($item[1] == '法人名') {
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $item['companyName'], $item['companyAddress'], $isFuzzy);
                } elseif ($item[1] == '個人名') {
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $item['personName'], '', $item['personAddress'], $isFuzzy, '');
                }
            }

        } elseif ( $fileType == "application/zip" ) {

            foreach ($cond['cond'] as $item) {
                if ($item[1] == '法人名') {
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $item['companyName'], $item['companyAddress'], $isFuzzy);
                } elseif ($item[1] == '個人名') {
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $item['personName'], '', $item['personAddress'], $isFuzzy, '');
                }
            }

        }

        return [
            'keyword' => $cond['cond'],
            'corporationList' => $corporationList,
            'personList' => $personList,
        ];

    }

    /**
     * PDFファイル名を取得
     *
     * @return string
     */
    public function getFileName(): string
    {
        $pdfName = '一括検索-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
    }

    /**
     * id指定レコードを取得
     *
     * @param $companyId
     * @param $batchId
     * @return object|null
     */
    public function getData($companyId, $batchId): object|null
    {
        $query = DB::table('tMngBatch');

        return $query->where('companyId', $companyId)->where('batchId', $batchId)->first();
    }

    /**
     * 入力PDFファイルからPDFファイルの作成
     *
     * @param $data
     * @throws Exception
     */
    public function makePdfFromPdf($data)
    {

        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);
        $executeDate = new Datetime($tMngBatchData['updateDateTime']);
        $executeDateString = $executeDate->format("Y/m/d h:i");

        $isHitSearch = false;
        $isHitCompany = false;
        $isHitPerson = false;
        $corporationListIndex = 0;
        $personListIndex = 0;

        foreach ($data['searchData']['keyword'] as $key => $item) {

            if ($item['type'] === "法人名") {
                // $data['searchData']['corporationList']に検索結果がないかチェックする
                $data['searchData']['keyword'][$key]['listIndex'] = $corporationListIndex;
                if (!empty($data['searchData']['corporationList'][$corporationListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $corporationListIndex++;

            } else if ($item['type'] === "個人名") {
                // $data['searchData']['personList']に検索結果がないかチェックする
                $data['searchData']['keyword'][$key]['listIndex'] = $personListIndex;
                if (!empty($data['searchData']['personList'][$personListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitPerson = true;
                }

                $personListIndex++;
            }
        }

        $pdfData = [
            'fileName' => $tMngBatchData['fileName'],
            'executeDate' => $executeDateString,
            'searchData' => $data['searchData'],
            'isHitSearch' => $isHitSearch,
            'isHitCompany' => $isHitCompany,
            'isHitPerson' => $isHitPerson,
            'uploadName' => $data['uploadName'],
        ];

        $pdfTemplate = "pdf.pdfBulkSearch";
        $fileName = $tMngBatchData['fileName'].'.pdf';
        if (!file_exists(storage_path('app/bulkSearch/download'))) {
            mkdir(storage_path('app/bulkSearch/download'));
        }
        $pdfPath = storage_path('app/bulkSearch/download') . '/'.$tMngBatchData['fileName'].'.pdf';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->setFont('ipamjm', '', 9);
        $pdf->AddPage();

        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());
        $pdf->Output($pdfPath, "F");

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
    }

    /**
     * 入力CSVファイルからPDFファイルの作成
     *
     * @param $data
     * @throws Exception
     */
    public function makePdfFromCsv($data)
    {

        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);
        $executeDate = new Datetime($tMngBatchData['updateDateTime']);
        $executeDateString = $executeDate->format("Y/m/d h:i");

        $isHitSearch = false;
        $isHitCompany = false;
        $isHitPerson = false;
        $corporationListIndex = 0;
        $personListIndex = 0;

        foreach ($data['searchData']['keyword'] as $key => $item) {

            if ($item['type'] === "法人検索") {
                $data['searchData']['keyword'][$key]['listIndex'] = $corporationListIndex;
                if (!empty($data['searchData']['corporationList'][$corporationListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $corporationListIndex++;

            } else if ($item['type'] === "個人検索") {
                $data['searchData']['searchData'][$key]['listIndex'] = $personListIndex;
                if (!empty($data['searchData']['personList'][$personListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitPerson = true;
                }

                $personListIndex++;
            }
        }

        $pdfData = [
            'fileName' => $tMngBatchData['fileName'],
            'executeDate' => $executeDateString,
            'searchData' => $data['searchData'],
            'isHitSearch' => $isHitSearch,
            'isHitCompany' => $isHitCompany,
            'isHitPerson' => $isHitPerson,
        ];

        $pdfTemplate = "pdf.pdfBulkSearchFromCsv";
        $fileName = $tMngBatchData['fileName'].'.pdf';
        if (!file_exists(storage_path('app/bulkSearch/download'))) {
            mkdir(storage_path('app/bulkSearch/download'));
        }
        $pdfPath = storage_path('app/bulkSearch/download') . '/'.$tMngBatchData['fileName'].'.pdf';

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->setFont('ipamjm', '', 9);
        $pdf->AddPage();

        $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());
        $pdf->Output($pdfPath, "F");

        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Transfer-Encoding: binary ");
        header('Content-Type: application/octet-streams');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
    }

    /**
     * 出力pdf用バッチテーブルの取得
     *
     * @param $companyId
     * @param $batchId
     * @return array $result
     */
    private function getTMngBatchData($companyId, $batchId): array
    {
        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);

        $tableData = $query->first();

        return [
            'fileName' => $tableData->fileName,
            'updateDateTime' => $tableData->updateDatetime,
        ];
    }


}
