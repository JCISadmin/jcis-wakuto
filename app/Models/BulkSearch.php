<?php

namespace App\Models;

use Exception;
use App\Exceptions\VaildException;
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

        $insData =[
            'companyId' =>$data['companyId'],
            'batchId' =>$data['batchId'],
            'searchCondition' =>$data['searchCondition'],
            'result' =>'未実行',
            'errorCode' =>'',
            'fileName' =>$data['fileName'],
            'createDatetime' =>$now,
            'updateDatetime' =>$now,
        ];

        DB::table($this->table)->insert($insData);
    }

    /**
     * 登記簿情報取得
     *
     * @param $filePath
     * @throws Exception
     */
    public function getRegistry($filePath)
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
                    $registry['corporateCode'][$i] = mb_convert_kana(str_replace($keys,$values,$contents[$j]), "n");
                }

                if ( strpos( $contents[$j], "商 号" ) ) {
                    
                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '商 号'=>'',
                        PHP_EOL=>'',
                    ];
                    
                    $keys = array_keys( $remove);
                    $values = array_values( $remove);
                    $registry['tradeName'][$i] = str_replace($keys,$values,$contents[$j]);
                }

                if ( strpos( $contents[$j], "本 店" ) ) {
                    
                    $remove = [
                        ' '=>'',
                        '┃'=>'',
                        '│'=>'',
                        '本 店'=>'',
                        PHP_EOL=>'',
                    ];

                    $keys = array_keys( $remove);
                    $values = array_values( $remove);

                    $str = mb_substr($contents[$j], 0, mb_strpos($contents[$j], '│', 15) );

                    $str = str_replace($keys,$values,$str);

                    $registry['mainShop'][$i] = $str;
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


                    $registry['CEOName'][$i][] = $str;
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

                    $registry['CEOAddress'][$i][]  = $str;
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

                    $registry['directorName'][$i][] = $str;
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

                    $registry['auditorName'][$i][] = $str;
                }
            }
        }

        // $items = [
        //     ['ファイル名','会社法人等番号','商号','本店所在地','代表取締役氏名','代表取締役住所','取締役氏名','監査役氏名'],
        // ];

        for($i=0; $i < count($registry['corporateCode']); $i++){
    
            $items[] = [$txtFileName[$i], $registry['corporateCode'][$i],'','','','','',''];
        }
        for($i=0; $i < count($registry['tradeName']); $i++){
    
            $items[] = [$txtFileName[$i], '', $registry['tradeName'][$i],'','','','',''];
        }
        for($i=0; $i < count($registry['mainShop']); $i++){
    
            $items[] = [$txtFileName[$i], '','', $registry['mainShop'][$i],'','','',''];
        }
        for($i=0; $i < count($registry['CEOName']); $i++){

            for($j=0; $j < count($registry['CEOName'][$i]); $j++){

                $items[] = [$txtFileName[$i], '','','', $registry['CEOName'][$i][$j],'','',''];
            }
    
        }
        for($i=0; $i < count($registry['CEOAddress']); $i++){

            for($j=0; $j < count($registry['CEOAddress'][$i]); $j++){
            
                $items[] = [$txtFileName[$i], '','','','', $registry['CEOAddress'][$i][$j],'',''];
            }
        }
        for($i=0; $i < count($registry['directorName']); $i++){

            for($j=0; $j < count($registry['directorName'][$i]); $j++){

                $items[] = [$txtFileName[$i], '','','','','', $registry['directorName'][$i][$j],''];
            }
        }
        for($i=0; $i < count($registry['auditorName']); $i++){

            for($j=0; $j < count($registry['auditorName'][$i]); $j++){

                $items[] = [$txtFileName[$i], '','','','','','', $registry['auditorName'][$i][$j]];
            }
        }

        return $items;
    }

    /**
     * テーブル検索
     *
     * @param $data
     * @throws Exception
     */
    public function search($cond, $companyId, $batchId)
    {
        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId)->where('batchId',$batchId)->update(['status' => '実行中']);

        for($i=0; $i < count($cond)-1; $i++){

            for($j=1; $j < count($cond[$i]); $j++){

                switch ($j) {
                    case 1:
                        $table = 'mCorporation';
                        $column = 'corporateCode';
                    case 2:
                        $table = 'mCorporation';
                        $column = 'inputName';
                    case 3:
                        $table = 'mCorporation';
                        $column = 'address';
                    case 4:
                        $table = 'mCorporation';
                        $column = 'delegate';
                    case 5:
                        $table = 'mPerson';
                        $column = 'address';
                    case 6:
                        $table = 'mPerson';
                        $column = 'inputName';
                    case 7:
                        $table = 'mPerson';
                        $column = 'inputName';
                }

                $query = DB::table($table);

                $data[] = $query->where($column , 'like', $cond[$i][$j])->get();
            }
        }

        $query = DB::table('tMngBatch');
        $query->where('companyId', $companyId)
        ->where('batchId',$batchId)
        ->update(['status' => '完了']);

        return $data;
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
     * @return array|null
     */
    public function getData($companyId, $batchId): ?array
    {
        $query = DB::table('tMngBatch');
        
        $query->where('companyId', $companyId);
        $query->where('batchId', $batchId);

        return $query->first();
    }
    
    
    /**
     * PDF生成
     *
     * @param $data
     * @throws Exception
     */
    public function makePDF($companyId, $batchId , $fileName)
    {
        $data = $this->getData($companyId, $batchId);

        $dt = new Datetime();
        $date = $dt->format('Y年n月j日');
        $data['batchId'] = $batchId;
        $data['printDate'] = $date;

        //PDF生成
        $pdfTemplate = 'pdf.pdfBulkSearch';
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true,"UTF-8");
        $pdf->SetFont('kozminproregular','',9);
        $pdf->setPrintHeader(false);
        $pdf->SetTopMargin(5);
        $pdf->AddPage();
        $pdf->writeHTML(view($pdfTemplate, $data)->render());
        $stream = $pdf->Output(  $fileName, "S" );

        return $stream;
    }

    /**
     * CSV生成
     *
     * @param $data
     * @throws Exception
     */
    public function makeCSV($data)
    {
        $data = $this->getOutputInfo($companyId, $batchId);

        $items = [
            '個人／法人名',
            '会社名',
            '法人番号',
            '会社住所',
            '構成員役職名',
            '役員名',
            '代表取締役住所',
            '結果(法)',
            '結果(個)',
            '結果(法＋個)',
            '該当個人名',
            '該当異名・かな',
            '生年月日',
            '現年齢',
            '当時郵便番号',
            '当時住所',
            '当時所属・役職',
            '当時所属団体名',
            '当時団体所在地',
            '事案年月日',
            '当時年齢',
            '処分官署',
            '要件区分',
            '事案概要',
            '該当法人名',
            '業種',
            '法人番号',
            '所在地の電話番号',
            '当時郵便番号',
            '当時所在地',
            '当時代表者',
            '当時実質経営者',
            '当時実質経営者所属',
            '事案年月日',
            '事案個人名',
            '処分官署',
            '要件区分',
            '事案概要',
        ];

        $items[] = '';





        
        




       
    }



}
