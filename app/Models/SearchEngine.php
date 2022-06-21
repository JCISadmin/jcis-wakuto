<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use TCPDF;
use Datetime;
use App\Models\SearchResultTcpdf;

/**
 * 検索用モデル
 */
class SearchEngine extends BaseModel
{

    /**
     * 共通フィルター文字
     * @var string[]
     */
    private array $filterChar = [
        ' ', '　',
        '-', '－',
        '/', '／',
        "'", '’',
        '=', '＝',
        '&', '＆',
        ',', '，',
        '.', '．',
        '·', '・',

    ];

    /**
     * 会社名専用フィルター文字
     * @var array|string[]
     */
    private array $filterCharCompany = [
        '社会保険労務士法人',
        '特定非営利活動法人',
        '有限責任中間法人', '無限責任中間法人',
        '地方独立行政法人',
        '医療法人社団', '医療法人財団', '社会医療法人',
        '一般財団法人', '公益財団法人',
        '一般社団法人', '公益社団法人',
        '社会福祉法人',
        '更生保護法人',
        '独立行政法人',
        '行政書士法人', '司法書士法人',
        '国立大学法人', '公立大学法人',
        '農事組合法人', '管理組合法人',
        '弁護士法人', '税理士法人',
        '株式会社',
        '有限会社',
        '合名会社',
        '合資会社',
        '合同会社',
        '医療法人',
        '財団法人',
        '社団法人',
        '宗教法人',
        '学校法人',
        '相互会社',
        'NPO法人',
        '（一財）', '(一財)',
        '（公財）', '(公財)',
        '（一社）', '(一社)',
        '（公社）', '(公社)',
        '（特非）', '(特非)',
        '（地独）', '(地独)',
        '（株）',  '(株)',
        '（有）', '(有)',
        '（名）', '(名)',
        '（資）', '(資)',
        '（同）', '(同)',
        '（医）', '(医)',
        '（財）', '(財)',
        '（社）', '(社)',
        '（宗）', '(宗)',
        '（学）', '(学)',
        '（福）', '(福)',
        '（相）', '(相)',
        '（独）', '(独)',
        '（弁）', '(弁)',
        '（中）', '(中)',
        '（行）', '(行)',
        '（司）', '(司)',
        '（税）', '(税)',
        '（大）', '(大)',
        '㈲', '㈱',
    ];


    /**
     * 異字体配列
     *
     * @var array
     */
    private array $convertFont = [];

    /**
     * 会社名のフィルター
     *
     * @param $name
     * @return array|string
     */
    public function filterCompany($name): array|string
    {

        $filterAry = array_merge($this->filterChar, $this->filterCharCompany);
        return str_replace($filterAry, '', $name);

    }

    /**
     * 個人名のフィルター
     *
     * @param $name
     * @return array|string
     */
    public function filterPerson($name): array|string
    {
        return str_replace($this->filterChar, '', $name);
    }

    /**
     * 法人情報検索
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $name
     * @param $city
     * @param $isFuzzy
     * @return array
     * @throws Exception
     */
    public function searchCompany($companyId, $contractPlanId, $userId, $name, $city, $isFuzzy): array
    {

        if ($name == '') {
            return [];
        }

        $keywordModel = new TKeywordHistory();
        $keywordModel->ins($companyId, $contractPlanId, $userId, hash('md5', $name));

        $nameList[] = $name;
        if ($isFuzzy) {
            $nameList = $this->convertFont($name);
        }

        $list = [];
        foreach ($nameList as $item) {
            $query = DB::table('mCorporation');
            $query->whereRaw('ucase(uniCaseName) = ucase(?)', [mb_convert_kana($item,"rnska")]);

            if ($city !== '') {
                $query->where('address', 'like', $city . '%');
            }

            $retList = $query->get();

            foreach ($retList as $retItem) {
                $list[] = (array)$retItem;
            }

        }

        //事案年月日をフォーマット
        if($list !== []){
            foreach($list as $idx => $value){
                $list[$idx]['formatCaseDate'] =  null;

                if(is_null($value['caseDate']) === false){
                    $list[$idx]['formatCaseDate'] =  $this->formatDate($value['caseDate']);
                }
            }

        }

        return $list;

    }

    /**
     * 個人名検索
     *
     * @param $companyId
     * @param $contractPlanId
     * @param $userId
     * @param $name
     * @param $age
     * @param $city
     * @param $isFuzzy
     * @param $birthday // YYYY-MM-DD
     * @return array
     * @throws Exception
     */
    public function searchPerson($companyId, $contractPlanId, $userId, $name, $age, $city, $isFuzzy, $birthday): array
    {
        if ($name == '') {
            return [];
        }

        $keywordModel = new TKeywordHistory();
        $keywordModel->ins($companyId, $contractPlanId, $userId, hash('md5', $name));

        $nameList[] = $name;
        if ($isFuzzy) {
            $nameList = $this->convertFont($name);
        }

        $caseAgeSql =<<<EOT
IF (
      birthday IS NOT NULL,
      TIMESTAMPDIFF(YEAR, mPerson.birthday, CURRENT_DATE()),
      IF ( ( caseDate IS NOT NULL ) AND ( caseAge IS NOT NULL ),
         caseAge + TIMESTAMPDIFF(YEAR, mPerson.caseDate, CURRENT_DATE()),
         NULL
      )
   ) as age
EOT;



        $list = [];
        foreach ($nameList as $item) {
            $inQuery = DB::table('mPerson');
            $inQuery->select(
                'mPerson.*',
                DB::raw($caseAgeSql)
            );

            /* @var string $inQuery */
            $query = DB::table($inQuery);

            $query->where(function($query) use($item) {
                $query->whereRaw('ucase(uniCaseName) = ucase(?)', [mb_convert_kana($item,"rnska")]);
                $query->orWhereRaw('ucase(uniCaseKana) = ucase(?)', [mb_convert_kana($item,"rnska")]);

            });

            if ($age !== '') {
                $query->whereBetween('age', [$age - 1, $age + 1]);
            }

            if ($city !== '') {
                $query->where('address', 'like', $city . '%');
            }

            if ($birthday !== '') {
                $query->where('birthday', $birthday);
            }

            $retList = $query->get();

            foreach ($retList as $retItem) {
                $list[] = (array)$retItem;
            }

        }

        //事案年月日・生年月日をフォーマット
        if($list !== []){
            foreach($list as $idx => $value){
                $list[$idx]['formatBirthday'] = null;
                $list[$idx]['formatCaseDate'] = null;

                if(is_null($value['birthday']) === false){
                    $list[$idx]['formatBirthday'] = $this->formatDate($value['birthday']);
                }
                if(is_null($value['caseDate']) === false){
                    $list[$idx]['formatCaseDate'] = $this->formatDate($value['caseDate']);
                }
            }

        }

        return $list;

    }

    /**
     * 異字体検索リスト作成
     *
     * @param $name
     * @return array
     */
    public function convertFont($name): array
    {
        $this->setConvertFont();

        $nameBlock = [];

        $nameAry = mb_str_split($name);
        foreach($nameAry as $key => $item) {
            $nameBlock[$key][] = $item;

            if (array_key_exists($item, $this->convertFont)) {
                foreach ($this->convertFont[$item] as $convertItem) {
                    $nameBlock[$key][] = $convertItem;
                }
            }
        }

        return $this->buildNameList($nameBlock);

    }

    /**
     * 計算結果PDFを取得
     *
     * @param $pdfData
     * @param $fileName
     * @return string
     */
    public function makePdf($pdfData, $fileName): string
    {

        // PDF生成
        $pdfTemplate = 'pdf.pdfSearch';
        $pdf = new SearchResultTcpdf(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
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

        return $pdf->Output($fileName, "S" );
    }

    /**
     * ファイル名を取得
     *
     * @return string
     */
    public function getFileName(): string
    {

        $pdfName = '検索結果-%s.pdf';
        $dlDate = date("Ymd");
        $fileName = sprintf($pdfName, $dlDate);
        return mb_convert_encoding($fileName, 'SJIS-WIN', 'UTF-8');

    }

    /**
     * 異字体検索リスト作成（再起処理）
     *
     * @param $nameBlock
     * @return array
     */
    private function buildNameList($nameBlock): array
    {
        $target = array_shift($nameBlock);

        $nameList = [];
        if (empty($nameBlock) === false) {
            $retList = $this->buildNameList($nameBlock);
            foreach ($target as $item) {
                foreach ($retList as $retItem) {
                    $nameList[] = $item . $retItem;
                }
            }

        } else {
            foreach ($target as $item) {
                $nameList[] = $item;
            }
        }

        return $nameList;
    }


    /**
     * 異字体配列の設定
     */
    private function setConvertFont()
    {

        if (count($this->convertFont) === 0) {
            $model = new MConvertFontDetail();
            $list = $model->getList();

            foreach ($list as $item) {
                $this->convertFont[$item->targetCharacter][] = $item->convertCharacter;
            }

        }

    }

    /**
     * 事案年月日・生年月日をフォーマット
     *
     * @param $editId
     * @return $formatted
     */
    public function formatDate($date) {

        $date = new DateTime($date);

        if(date_format($date, 'nj') === '11'){
            //1月1日 ==> yyyy
            $formatted = date_format($date, 'Y');

        }elseif(date_format($date, 'j') === '1'){
            //X月1日 ==> yyyy/mm
            $formatted = date_format($date, 'Y/m');

        }else{
            //yyyy/mm/dd
            $formatted = date_format($date, 'Y/m/d');
        }

        return $formatted;

    }

}
