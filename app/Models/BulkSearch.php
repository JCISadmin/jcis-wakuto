<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

/** @noinspection PhpComposerExtensionStubsInspection */

namespace App\Models;

use Exception;
use Datetime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use TCPDF;
use App\Models\SearchResultTcpdf;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

    const END_CONTENT_MARK = '───';

    const CORPORATE_CODE_TITLE = ['会社法人等番号'];
    const COMPANY_NAME_TITLE = ['商号','名称'];
    const COMPANY_ADDRESS_TITLE = ['本店','主たる事務所'];
    const PERSON_TITLE = ['役員に関する事項'];

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
            if(file_exists($value->searchCondition) === false ){
                $searchData = json_decode($value->searchCondition , true);
            }else{
                $jsonData = file_get_contents($value->searchCondition);
                if (!$jsonData) {
                    throw new Exception('file_get_contents() Error');
                }    
                $jsonData = mb_convert_encoding($jsonData, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
                $searchData = json_decode($jsonData , true);
            }
            $value->type = $searchData['type'];
            $value->uploadName = $searchData['uploadName'];
        }

        return $list;
    }

    /**
     * 登記簿情報から配列を作成
     *
     * @param $filePath
     * @param $uploadName
     * @param $searchRepFlg
     * @param $retireFlg
     * @return array
     */
    public function getRegistryData($files, $uploadName,$searchRepFlg,$retireFlg): array
    {

        $registryData = [];

        //代表のみ検索フラグ
        $isRepresentative = false;
        if($searchRepFlg === 'true'){
            $isRepresentative = true;
        }
        //退任フラグ
        $isRetire = false;
        if($retireFlg === 'true'){
            $isRetire = true;
        }

        foreach($files as $fileIdx => $file){

            //pdftotextで作成したtxtファイルを取得
            $txtFilePath = substr($file, 0, -3) . 'txt';
            $txtFileName[$fileIdx] = basename($txtFilePath);
            $contents = file($txtFilePath);

            //記号を統一
            $contents = str_replace(config('hds.registryInfo.replaceSymbol.verticalLine'),'│',$contents);
            $contents = str_replace(config('hds.registryInfo.replaceSymbol.horizonLine'),'─',$contents);
            $contents = str_replace(config('hds.registryInfo.replaceSymbol.other'),'│',$contents);
            //不要文字を削除
            $contents = str_replace(config('hds.registryInfo.removeSymbol'), '', $contents);

            // │ で文字列を分割
            foreach($contents as $key => $line) {
                if (mb_substr($line, 0, 1) == '│') {
                    $line = mb_substr($line, 1);
                }
                if (mb_substr($line, -1, 1) == '│') {
                    $line = mb_substr($line, 0, mb_strlen($line) - 1);
                }
                $contents[$key] = explode('│', $line);
            }

            $corporateCode = '';
            $companyName = '';
            $companyAddress = '';
            $personAry = [];

            //登記簿解析
            for($lineIdx = 0; $lineIdx < count($contents); $lineIdx++){
                if( in_array($contents[$lineIdx][0],self::CORPORATE_CODE_TITLE) ){
                    //法人番号を取得
                    $corporateCode = $contents[$lineIdx][1];
                    continue;
                }
                if( in_array($contents[$lineIdx][0],self::COMPANY_NAME_TITLE) ){
                    //法人名を取得
                    while(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) === false){
                        $name = $contents[$lineIdx][1];
                        $lineIdx += 1;
                        while(mb_strpos($contents[$lineIdx][1],self::END_CONTENT_MARK) === false){
                            $name = $name.$contents[$lineIdx][1];
                            $lineIdx++;
                        }
                        $nameAry[] = $name;
                        if(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) !== false){
                            //[1]終了時に[0]も終了している場合終了
                            break;
                        }
                        $lineIdx++;
                    }
                    $companyName = end($nameAry);
                    continue;
                }
                if( in_array($contents[$lineIdx][0],self::COMPANY_ADDRESS_TITLE) ){
                    //法人住所を取得
                    while(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) === false){
                        $address = $contents[$lineIdx][1];
                        $lineIdx += 1;
                        while(mb_strpos($contents[$lineIdx][1],self::END_CONTENT_MARK) === false){
                            $address = $address.$contents[$lineIdx][1];
                            $lineIdx++;
                        }
                        $addressAry[] = $address;
                        if(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) !== false){
                            //[1]終了時に[0]も終了している場合終了
                            break;
                        }
                        $lineIdx++;
                    }
                    $companyAddress = end($addressAry);
                    continue;
                }
                if( in_array($contents[$lineIdx][0],self::PERSON_TITLE) ){
                    //個人名を取得
                    $cnt = -1;
                    while(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) === false){
                        if($this->isBlackList($contents[$lineIdx][1])){
                            //除外文字を含む場合
                            while(mb_strpos($contents[$lineIdx][1],self::END_CONTENT_MARK) === false){
                                $lineIdx++;
                            }
                            if(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) !== false){
                                //[1]終了時に[0]も終了している場合終了
                                break;
                            }
                            $lineIdx++;
                            continue;
                        }
                        
                        $cnt++;
                        $personAry[$cnt] = [
                            'position' => '',
                            'name' => '',
                            'address' => '',
                            'retireFlag' => false,
                            'addressFlag' => false,
                        ];

                        while(mb_strpos($contents[$lineIdx][1],self::END_CONTENT_MARK) === false){
                            if($contents[$lineIdx][1] !== ''){
                                $pos = $this->isPosition($contents[$lineIdx][1]);
                                if($pos !== false){
                                    //役職文字を含む場合
                                    $personAry[$cnt]['position'] = $pos;
                                    $personAry[$cnt]['name'] = str_replace($pos,'',$contents[$lineIdx][1]);
                                    $personAry[$cnt]['addressFlag'] = true;
                                }else{
                                    //役職文字を含まない場合、住所として取得
                                    if($personAry[$cnt]['addressFlag'] === true){
                                        $personAry[$cnt]['address'] = '';
                                        $personAry[$cnt]['addressFlag'] = false;
                                    }
                                    $personAry[$cnt]['address'] = $personAry[$cnt]['address'].$contents[$lineIdx][1];
                                }
                            }
                            if( isset($contents[$lineIdx][2]) ){
                                //退任フラグ更新
                                if( $this->isRetire($contents[$lineIdx][2]) ){
                                    $personAry[$cnt]['retireFlag'] = true;
                                }
                                if( $this->isAppoint($contents[$lineIdx][2]) ){
                                    $personAry[$cnt]['retireFlag'] = false;
                                }
                            }
                            $lineIdx++;
                        }
                        if(mb_strpos($contents[$lineIdx][0],self::END_CONTENT_MARK) !== false){
                            //[1]終了時に[0]も終了している場合終了
                            break;
                        }
                        $lineIdx++;
                    }
                }
            }

            //法人情報
            if( $companyName === ''){
                //会社名が取得できないものがある場合終了
                return [];
            }

            $registryData[$fileIdx][] = [
                'fileName' => $txtFileName[$fileIdx],
                'type' => '法人検索',
                'position' => '法人',
                'companyName' => $companyName,
                'corporateCode' => $corporateCode,
                'companyAddress' => $companyAddress,
                'uploadName' => $uploadName == '' ? $txtFileName[$fileIdx] : $uploadName,
            ];

            $wkPersonAry = [];
            foreach($personAry as $person){
                if($isRetire && $person['retireFlag'] === true){
                    //辞任・退任をスキップ
                    continue;
                }
                if($isRepresentative && !in_array($person['position'],config('hds.registryInfo.position.representative'))){
                    //「法人・代表者のみ検索」にチェックあり 代表役職以外をスキップ
                    continue;
                }
                $wkPersonAry[] = $person;
            }

            $dupCheckAry = [];
            foreach($wkPersonAry as $wkPerson){
                //法人分1つずらす
                $dupIdx = array_search($wkPerson['name'],array_column($dupCheckAry,'name')) +1;
                if($dupIdx === false){
                    //氏名が重複しない場合
                    $registryData[$fileIdx][] = [
                        'fileName' => $txtFileName[$fileIdx],
                        'type' => '個人検索',
                        'position' => $wkPerson['position'],
                        'personName' => $wkPerson['name'],
                        'personAddress' => $wkPerson['address'],
                        'uploadName' => $uploadName == '' ? $txtFileName[$fileIdx] : $uploadName,
                    ];
                    $dupCheckAry[] = [
                        'name' => $wkPerson['name'],
                        'position' => $wkPerson['position'],
                    ];
                }else{
                    //氏名が重複する場合
                    if(in_array($wkPerson['position'],config('hds.registryInfo.position.representative'))){
                        //追加するデータの['position']が代表
                        $registryData[$fileIdx][$dupIdx] = [
                            'fileName' => $txtFileName[$fileIdx],
                            'type' => '個人検索',
                            'position' => $wkPerson['position'],
                            'personName' => $wkPerson['name'],
                            'personAddress' => $wkPerson['address'],
                            'uploadName' => $uploadName == '' ? $txtFileName[$fileIdx] : $uploadName,
                        ];
                    }else{
                        if(in_array($registryData[$fileIdx][$dupIdx]['position'],config('hds.registryInfo.position.representative'))){
                            //追加先データの['position']が代表
                            continue;
                        }else{
                            $registryData[$fileIdx][$dupIdx] = [
                                'fileName' => $txtFileName[$fileIdx],
                                'type' => '個人検索',
                                'position' => $wkPerson['position'],
                                'personName' => $wkPerson['name'],
                                'personAddress' => $wkPerson['address'],
                                'uploadName' => $uploadName == '' ? $txtFileName[$fileIdx] : $uploadName,
                            ];    
                        }
                    }
                }
            }
        }

        return $registryData;
    }

    /**
     * 個人情報 除外チェック
     *
     * @param $line
     * @throws Exception
     */
    public function isBlackList($line)
    {
        foreach(config('hds.registryInfo.position.exclusion') as $blackList){
            if(preg_match("/^$blackList.*$/", $line) !== 0){
                return true;
            }
        }
        return false;
    }

    /**
     * 役職チェック
     *
     * @param $line
     * @throws Exception
     */
    public function isPosition($line)
    {
        foreach(config('hds.registryInfo.position.representative') as $pos){
            if(preg_match("/^$pos.*$/", $line) !== 0){
                return $pos;
            }
        }
        foreach(config('hds.registryInfo.position.normal') as $pos){
            if(preg_match("/^$pos.*$/", $line) !== 0){
                return $pos;
            }
        }
        return false;
    }

    /**
     * 退任チェック
     *
     * @param $line
     * @throws Exception
     */
    public function isRetire($line)
    {
        foreach(config('hds.registryInfo.retire') as $retire){
            if( mb_strpos($line,$retire) !== false ){
                return true;
            }
        }
        return false;
    }

    /**
     * 就任・重任チェック
     *
     * @param $line
     * @throws Exception
     */
    public function isAppoint($line)
    {
        foreach(config('hds.registryInfo.appoint') as $appoint){
            if( mb_strpos($line,$appoint) !== false ){
                return true;
            }
        }
        return false;
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

        $corporationList = [];
        $personList = [];
        $retAry = [];
        
        $isFuzzy = false;
        if ($cond['fuzzyFlg'] === 'true') {
            $isFuzzy = true;
        }

        if ( $fileType === "application/csv" ) {

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

        } elseif ( $fileType === "application/pdf" || $fileType === "application/zip" ||  $fileType === "registry/csv") {

            foreach ($cond['cond'] as $fileItem) {

                $corporationList = [];
                $personList = [];

                foreach ($fileItem as $item) {
                    if ($item['type'] === '法人検索') {
                        $name = $model->filterCompany($item['companyName']);
                        $corporationList[] = $model->searchCompany($companyId, $contractPlanId, $userId, $name, $item['companyAddress'], $isFuzzy);
                    } elseif ($item['type'] === '個人検索') {
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

                if ($item['type'] === '法人検索') {
                    // $data['searchData'][$fileKey]['corporationList']に検索結果がないかチェックする
                    $data['searchData'][$fileKey]['keyword'][$key]['listIndex'] = $corporationListIndex;
                    if (!empty($data['searchData'][$fileKey]['corporationList'][$corporationListIndex])) {
                        $data['searchData'][$fileKey]['keyword'][$key]['hitSign'] = '○';
                        $isHitSearch[$fileKey] = true;
                        $isHitCompany[$fileKey] = true;
                    }

                    $corporationListIndex++;

                } else if ($item['type'] === '個人検索') {
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

        $pdf = new SearchResultTcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();

        $pdf->SetFont('ipamjm', 'B', 15);
        $pdf->Text(10, 15, "JCIS WEBDB ver.3-反社データベース WEB即時チェックシステム",0.3, false, true, 0, 0, 'C');
        //タイトル下幅調整
        $pdf->Text(0, 20, "　");
        $pdf->SetFont('ipamjm', '', 9);
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
        $splitNum = config('hds.bulkSearch.maxDispNum.pdfFromCsv');//1ファイルに出力される最大検索結果数

        foreach ($data['searchData']['keyword'] as $key => $item) {

            if ($item['type'] === "法人検索") {
                $type = '法人検索';
                $data['searchData']['keyword'][$key]['listIndex'] = $corporationListIndex;
                if (!empty($data['searchData']['corporationList'][$corporationListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitCompany = true;
                }

                $chunkData['searchData'] = array_chunk($data['searchData']['corporationList'], $splitNum, true);
                $corporationListIndex++;

            } else if ($item['type'] === "個人検索") {
                $type = '個人検索';
                $data['searchData']['keyword'][$key]['listIndex'] = $personListIndex;
                if (!empty($data['searchData']['personList'][$personListIndex])) {
                    $data['searchData']['keyword'][$key]['hitSign'] = '○';
                    $isHitSearch = true;
                    $isHitPerson = true;
                }

                $chunkData['searchData'] = array_chunk($data['searchData']['personList'], $splitNum, true);
                $personListIndex++;
            }
        }

        $chunkData['keyword'] = array_chunk($data['searchData']['keyword'], $splitNum, true);

        for($fileNo = 1; $fileNo <= count($chunkData['keyword']); $fileNo++){
            $data['searchData']['keyword'] = $chunkData['keyword'][$fileNo-1];
            $data['searchData']['personList'] = !empty($data['searchData']['personList']) ? $chunkData['searchData'][$fileNo-1] : $data['searchData']['personList'];
            $data['searchData']['corporationList'] = !empty($data['searchData']['corporationList']) ? $chunkData['searchData'][$fileNo-1] : $data['searchData']['corporationList'];

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
            $pdfName = $data['uploadName'].'_'.$fileNo.'.pdf';
            if (!file_exists(storage_path('app/bulkSearch/download'.'/'.$tMngBatchData['fileName']))) {
                mkdir(storage_path('app/bulkSearch/download'.'/'.$tMngBatchData['fileName']));
            }
            $pdfPath = storage_path('app/bulkSearch/download'.'/'.$tMngBatchData['fileName']) . '/'.$pdfName;

            $pdf = new SearchResultTcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', true);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setPrintHeader(false);
            $pdf->SetTopMargin(5);
            $pdf->AddPage();

            $pdf->SetFont('ipamjm', 'B', 15);
            $pdf->Text(10, 15, "JCIS WEBDB ver.3-反社データベース WEB即時チェックシステム",0.3, false, true, 0, 0, 'C');
            //タイトル下幅調整
            $pdf->Text(0, 20, "　");
            $pdf->SetFont('ipamjm', '', 9);
            $pdf->writeHTML(view($pdfTemplate, $pdfData)->render());
            $pdf->Output($pdfPath, "F");

        }

        $files = glob(storage_path('app/bulkSearch/download'.'/'. $tMngBatchData['fileName'].'/*') );
        $zip = new ZipArchive();
        $zip->open(storage_path('app/bulkSearch/download').'/'. $tMngBatchData['fileName'].'.zip', ZipArchive::CREATE);

        foreach($files as $file){
            $fileInfo = pathinfo($file);
            $fileName = $fileInfo['filename'].'.'.$fileInfo['extension'];
            $zip->addFile($file, $fileName);
        }

        $zip->close();

        $dir = 'bulkSearch/download/'.$tMngBatchData['fileName'];
        Storage::deleteDirectory($dir);

        unset($pdf);
        unset($zip);
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
