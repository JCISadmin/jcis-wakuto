<?php

namespace App\Models;

use Exception;
use App\Exceptions\VaildException;
use Datetime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use TCPDF;
use App\Models\SearchEngine;

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


    private array $errorMsg = [
        1 => '',
        2 => '',
        3 => '',
        4 => '',
        5 => '',
        6 => '',
        7 => '',
        8 => '',
        9 => '',
        10 => '',
        11 => '',
        12 => '',
        13 => '',
        14 => '',
        15 => '',
        16 => '',
        17 => '',
        18 => '',
        19 => '',
        20 => '',
        21 => '',
    ];

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
     * @param $fileName
     * @return array
     * @throws VaildException|Exception
     */
    public function import(): array
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
     * 行単位更新処理
     *
     * @param $data
     * @param $rawCnt
     * @return bool
     * @throws Exception
     */
    private function updateData($data, $rawCnt): bool
    {

        $this->begin();


        $this->commit();
        return true;
    }

    /**
     * 一覧取得
     *
     * @param $inputName
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
     * テーブル　データ保存
     *
     * @param $data
     * @throws Exception
     */
    public function insData($data)
    {

        $dt = new Datetime();
        $now = $dt->format('Y-m-d');

        $insData = [
            'companyId' =>$data['companyId'],
            'batchId' =>$data['batchId'],
            'searchCondition' =>$data['searchCondition'],
            'result' =>'未実行',
            'errorCode' =>'',
            'fileName' =>$data['companyId'] . $data['batchId'],
            'createDatetime' =>$now,
            'updateDatetime' =>$now,
        ];

        DB::table($this->table)->insert($insData);
    }

    /**
     * 登記簿情報からCSV配列を作成
     *
     * @param $filePath
     * @throws Exception
     */
    public function RegistryCSVData($filePath)
    {
        $registry = [];

        for($i=0 ; $i < count($filePath); $i++){

            $filePath[$i] = substr($filePath[$i], 0, -3) . 'txt';

            $txtFileName[$i] = basename($filePath[$i]);
        
            $contents = file($filePath[$i]);

            for($j=0; $j < count($contents); $j++){

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

                        $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 8) );
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

                    $str = mb_substr($str, mb_strpos($contents[$j], '取締役', 0));
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
                $txtFileName[$i],
                '法人名',
                $registry['companyName'],
                $registry['corporateCode'],
                $registry['companyAddress'],                
            ];

            if(isset($registry['directorName'])){

                foreach($registry['directorName'] as $directorName){
                    
                    $csvData[] = [
                        $txtFileName[$i],
                        '個人名',
                        '取締役',
                        $directorName,
                        ''
                    ];
                }
            }

            
            if(isset($registry['CEOName'])){

                foreach($registry['CEOName'] as $key => $CEOName){
                    
                    $csvData[] = [
                        $txtFileName[$i],
                        '個人名',
                        '代表取締役',
                        $CEOName,
                        $registry['CEOAddress'][$key]
                    ];
                }
            }

            if(isset($registry['auditorName'])){

                foreach($registry['auditorName'] as $auditorName){
                    
                    $csvData[] = [
                        $txtFileName[$i],
                        '個人名',
                        '監査役',
                        $auditorName,
                        ''
                ];
                }
            }

        }
        
        return $csvData;
    }

    /**
     * テーブル検索
     *
     * @param $data
     * @throws Exception
     */
    public function search($cond, $batchId, $companyId, $contractPlanId, $userId, $fileType)
    {
        $model = new SearchEngine();
        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId)->where('batchId',$batchId)->update(['result' => '実行中']);

        $isFuzzy = '';

        if(end($cond)[0] == 'on'){

            $isFuzzy = true;
        }

        if( $fileType == "application/csv" ){
            
            for($i=0; $i < count($cond) - 1; $i++){

                if($cond[$i][0] == '法人検索'){

                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $cond[$i][1], '', $isFuzzy);

                }elseif($cond[$i][0] == '個人検索'){
                    
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $cond[$i][1], '', '', $isFuzzy, $cond[$i][2]);
                }
            }

            
        }elseif( $fileType == "application/pdf" ){
            
            for($i=0; $i < count($cond) - 1; $i++){

                if($cond[$i][1] == '法人名'){
                    
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $cond[$i][2], $cond[$i][4], $isFuzzy);
                    
                }elseif($cond[$i][1] == '個人名'){
                    
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $cond[$i][3], '', $cond[$i][4], $isFuzzy, '');
                }
            }
            
        }elseif( $fileType == "application/zip" ){

            for($i=0; $i < count($cond) - 1; $i++){

                if($cond[$i][1] == '法人名'){
                    
                    $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $cond[$i][2], $cond[$i][4], $isFuzzy);
                    
                }elseif($cond[$i][1] == '個人名'){
                    
                    $personList[] = $model->searchPerson($companyId, $contractPlanId, $userId, $cond[$i][3], '', $cond[$i][4], $isFuzzy, '');
                }
            }
        }

        $result =[
            $cond,
            $corporationList,
            $personList,
        ];
        
        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId)
        ->where('batchId',$batchId)
        ->update(['result' => '完了']);

        return $result;
    }

    /**
     * PDFファイル名を取得
     *
     * @return string
     */
    public function getFileName()
    {
        $pdfName = '一括検索-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        $fileName = mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');
        
        return $fileName;
    }

    /**
     * id指定レコードを取得
     *
     * @param $companyId
     * @param $batchId

     */
    public function getData($companyId, $batchId)
    {
        $query = DB::table('tMngBatch');
        
        return $query->where('companyId', $companyId)->where('batchId', $batchId)->first();
    }
    
    /**
     * 入力PDFファイルからPDFファイルの作成
     *
     * @param array $data
     */
    public function makePdfFromPdf($data)
    {

        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);
        $executeDate = new Datetime($tMngBatchData['updateDateTime']);
        $executeDateString = $executeDate->format("Y/m/d h:i");

        $isHitSearch = false;
        $isHitCompany = false;
        $isHitPerson = false;
        $companyCount = 0;
        $personCount = 0;
        foreach ($data['searchData'][0] as $key => $item) {

            if ($item[1] === "法人名") {
                $data['searchData'][0][$key][5] = $companyCount;
                if (!empty($data['searchData'][1][$companyCount])) {
                    $data['searchData'][0][$key][6] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $companyCount++;
            } else if ($item[1] === "個人名") {
                $data['searchData'][0][$key][5] = $personCount;
                if (!empty($data['searchData'][2][$personCount])) {
                    $data['searchData'][0][$key][6] = '○';
                    $isHitSearch = true;
                    $isHitPerson = true;
                }

                $personCount++;
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
     * @param array $data
     */
    public function makePdfFromCsv($data)
    {

        $tMngBatchData = $this->getTMngBatchData($data['companyId'], $data['batchId']);
        $executeDate = new Datetime($tMngBatchData['updateDateTime']);
        $executeDateString = $executeDate->format("Y/m/d h:i");

        $isHitSearch = false;
        $isHitCompany = false;
        $isHitPerson = false;
        $companyCount = 0;
        $personCount = 0;
        foreach ($data['searchData'][0] as $key => $item) {

            if ($item[0] === "法人検索") {
                $data['searchData'][0][$key][3] = $companyCount;
                if (!empty($data['searchData'][1][$companyCount])) {
                    $data['searchData'][0][$key][4] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $companyCount++;
            } else if ($item[0] === "個人検索") {
                $data['searchData'][0][$key][3] = $personCount;
                if (!empty($data['searchData'][2][$personCount])) {
                    $data['searchData'][0][$key][4] = '○';
                    $isHitSearch = true;
                    $isHitPerson = true;
                }

                $personCount++;
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
     * @param string $companyId
     * @param string $batchId
     * @return array $result
     */
    private function getTMngBatchData($companyId, $batchId) {
        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);

        $tableData = $query->first();

        $result = [
            'fileName' => $tableData->fileName,
            'updateDateTime' => $tableData->updateDatetime,
        ];
        return $result;
    }


}
