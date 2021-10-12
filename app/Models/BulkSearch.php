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
     * @param $companyId
     * @param $pageLine
     * @return LengthAwarePaginator
     */
    public function getList($companyId, $pageLine): LengthAwarePaginator
    {

        if ($pageLine == '') {
            $pageLine = self::PAGE_LINE;
        }

        $query = DB::table($this->table);
        $query->where('companyId', $companyId);
        $query->orderByDesc('createDatetime');

        $list = $query->paginate($pageLine);

        foreach ($list as $value) {
            $searchData = json_decode($value->searchCondition, true);
            $value->type = $searchData['type'];
            $value->uploadName = $searchData['uploadName'];
        }

        return $list;
    }

    /**
     * 登記簿情報からCSV配列を作成
     *
     * @param $filePath
     * @param $uploadName
     * @return array
     */
    public function RegistryCSVData($filePath, $uploadName): array
    {

        $csvData = [];

        for($i = 0 ; $i < count($filePath); $i++){

            $csvData[$i] = [];
            $registry = [];

            $filePath[$i] = substr($filePath[$i], 0, -3) . 'txt';
            $txtFileName[$i] = basename($filePath[$i]);
            $contents = file($filePath[$i]);

            for($j = 0; $j < count($contents); $j++){
                $contents[$j] = str_replace('　', ' ', $contents[$j]);

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

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 28) );
                    if(is_null($str)){

                        $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '┃', 28) );
                    }

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

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '├', 28) );

                    if(is_null($str)){

                        $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 28) );
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

                    $str = mb_substr($contents[$j-1], 0, mb_strpos($contents[$j-1], '│', 28) );
                    if(is_null($str)){

                        $str = mb_substr($contents[$j-1], 0, mb_strpos($contents[$j-1], '├', 28) );
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

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 28) );

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

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 28) );

                    $str = str_replace($keys,$values,$str);

                    $registry['auditorName'][] = $str;
                }
            }

            if(empty($registry['companyName'])){
                return [];
            }

            $csvData[$i][] = [
                'fileName' => $txtFileName[$i],
                'type' => '法人名',
                'position' => '法人名',
                'companyName' => $registry['companyName'],
                'corporateCode' => isset($registry['corporateCode']) ? $registry['corporateCode'] : '',
                'companyAddress' => isset($registry['companyAddress']) ? $registry['companyAddress'] : '',
                'uploadName' => $uploadName == '' ? $txtFileName[$i] : $uploadName,
            ];

            if(isset($registry['CEOName'])){

                foreach($registry['CEOName'] as $key => $CEOName){

                    $csvData[$i][] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '代表取締役',
                        'personName' => $CEOName,
                        'personAddress' => $registry['CEOAddress'][$key],
                        'uploadName' => $uploadName == '' ? $txtFileName[$i] : $uploadName,
                    ];
                }
            }

            if(isset($registry['directorName'])){

                foreach($registry['directorName'] as $directorName){

                    $csvData[$i][] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '取締役',
                        'personName' => $directorName,
                        'personAddress' => '',
                        'uploadName' => $uploadName == '' ? $txtFileName[$i] : $uploadName,
                    ];
                }
            }


            if(isset($registry['auditorName'])){

                foreach($registry['auditorName'] as $auditorName){

                    $csvData[$i][] = [
                        'fileName' => $txtFileName[$i],
                        'type' => '個人名',
                        'position' => '監査役',
                        'personName' => $auditorName,
                        'personAddress' => '',
                        'uploadName' => $uploadName == '' ? $txtFileName[$i] : $uploadName,
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
        $retAry = [];

        if ($cond['fuzzyFlg'] == 'on') {
            $isFuzzy = true;
        }

        if ( $fileType == "application/csv" ) {

            foreach ($cond['cond'] as $item) {
                if ($item['type'] === '法人検索') {
                    $name = $model->filterCompany($item['name']);
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $name, '', $isFuzzy);

                } elseif ($item['type'] === '個人検索') {
                    $name = $model->filterPerson($item['name']);
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $name, '', '', $isFuzzy, $item['birthday']);

                }
            }

            return [
                'keyword' => $cond['cond'],
                'corporationList' => $corporationList,
                'personList' => $personList,
            ];

        } elseif ( $fileType == "application/pdf" || $fileType == "application/zip") {

            foreach ($cond['cond'] as $fileItem) {

                $corporationList = [];
                $personList = [];

                foreach ($fileItem as $item) {
                    if ($item['type'] == '法人名') {
                        $name = $model->filterCompany($item['companyName']);
                        $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $name, $item['companyAddress'], $isFuzzy);
                    } elseif ($item['type'] == '個人名') {
                        $name = $model->filterPerson($item['personName']);
                        $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $name, '', $item['personAddress'], $isFuzzy, '');
                    }
                }

                $retAry[] = [
                    'keyword' => $fileItem,
                    'corporationList' => $corporationList,
                    'personList' => $personList,
                ];


            }

            return $retAry;
        }

        return [];

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
     * 印字データ生成
     *
     * @param $data
     * @return array
     * @throws Exception
     */
    public function makePrintDate($data): array
    {
        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);
        $executeDate = new Datetime($tMngBatchData['updateDateTime']);
        $executeDateString = $executeDate->format("Y/m/d H:i");

        $isHitSearch = [];
        $isHitCompany = [];
        $isHitPerson = [];

        foreach ($data['searchData'] as $fileKey => $fileItem) {

            $isHitSearch[$fileKey] = false;
            $isHitCompany[$fileKey] = false;
            $isHitPerson[$fileKey] = false;

            $corporationListIndex = 0;
            $personListIndex = 0;

            foreach ($fileItem['keyword'] as $key => $item) {

                if ($item['type'] === "法人名") {
                    // $data['searchData'][$fileKey]['corporationList']に検索結果がないかチェックする
                    $data['searchData'][$fileKey]['keyword'][$key]['listIndex'] = $corporationListIndex;
                    if (!empty($data['searchData'][$fileKey]['corporationList'][$corporationListIndex])) {
                        $data['searchData'][$fileKey]['keyword'][$key]['hitSign'] = '○';
                        $isHitSearch[$fileKey] = true;
                        $isHitCompany[$fileKey] = true;
                    }

                    $corporationListIndex++;

                } else if ($item['type'] === "個人名") {
                    // $data['searchData'][$fileKey]['personList']に検索結果がないかチェックする
                    $data['searchData'][$fileKey]['keyword'][$key]['listIndex'] = $personListIndex;
                    if (!empty($data['searchData'][$fileKey]['personList'][$personListIndex])) {
                        $data['searchData'][$fileKey]['keyword'][$key]['hitSign'] = '○';
                        $isHitSearch[$fileKey] = true;
                        $isHitPerson[$fileKey] = true;
                    }

                    $personListIndex++;
                }
            }

        }

        return [
            'fileName' => $tMngBatchData['fileName'],
            'executeDate' => $executeDateString,
            'searchData' => $data['searchData'],
            'isHitSearch' => $isHitSearch,
            'isHitCompany' => $isHitCompany,
            'isHitPerson' => $isHitPerson,
            'uploadName' => $data['uploadName'],
        ];

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

        $pdfData = $this->makePrintDate($data);

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
        $executeDateString = $executeDate->format("Y/m/d H:i");

        $isHitSearch = false;
        $isHitCompany = false;
        $isHitPerson = false;
        $corporationListIndex = 0;
        $personListIndex = 0;
        $type = '';

        foreach ($data['searchData']['keyword'] as $key => $item) {

            if ($item['type'] === "法人検索") {
                $type = '法人検索';
                $data['searchData']['keyword'][$key]['listIndex'] = $corporationListIndex;
                if (!empty($data['searchData']['corporationList'][$corporationListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $corporationListIndex++;

            } else if ($item['type'] === "個人検索") {
                $type = '個人検索';
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
            'type' => $type,
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
    protected function getTMngBatchData($companyId, $batchId): array
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
