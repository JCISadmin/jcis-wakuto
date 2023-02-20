<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\DB;
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
     * 会社名専用フィルター文字(英字)
     * @var array|string[]
     */
    private array $filterCharCompanyEn = [
        //文字数の多い方を上部に定義してください。
        ' Co\., Ltd\.',
        ',Co\., Ltd\.',
        ' Co\. Ltd\.',
        ',Co\. Ltd\.',
        ' Inc\.',
        ',Inc\.',
        ' Corp\.',
        ',Corp\.',
        ' limited partnership company',
        ',limited partnership company',
        ' limited partnership',
        ',limited partnership',
        ' General Partnership Company',
        ',General Partnership Company',
        ' General Partnership',
        ',General Partnership',
        ' Unlimited Partnership Company',
        ',Unlimited Partnership Company',
        ' Unlimited Partnership',
        ',Unlimited Partnership',
        ' LLC\.',
        ',LLC\.',
        ' healthcare corporation',
        ',healthcare corporation',
        ' medical corporation',
        ',medical corporation',
        ' association',
        ',association',
        ' foundation',
        ',foundation',
        ' social welfare corporation',
        ',social welfare corporation',
        ' social welfare juridical person',
        ',social welfare juridical person',
        ' Specified Nonprofit Corporation',
        ',Specified Nonprofit Corporation',
        ' Approved Specified Nonprofit Corporation',
        ',Approved Specified Nonprofit Corporation',
        ' University',
        ',University',
        ' B\.V\.',
        ',B\.V\.',
        ' SDN\.BHD\.',
        ',SDN\.BHD\.',
        ' A\.S\.',
        ',A\.S\.',
        ' CO\.',
        ',CO\.',
        ' PTE\. LTD\.',
        ',PTE\. LTD\.',
        ' S\.A\.U\.',
        ',S\.A\.U\.',
        ' CORP S\.A\. DE C\.V\.',
        ',CORP S\.A\. DE C\.V\.',
        ' LIMITED',
        ',LIMITED',
        ' N\.V',
        ',N\.V',
        ' S\.A\.',
        ',S\.A\.',
        ' L\.P\.',
        ',L\.P\.',
        ' Corporation',
        ',Corporation',
        ' TRUST',
        ',TRUST',
        ' COMPANY',
        ',COMPANY',
        ' CORP\.',
        ',CORP\.',
        ' GmbH',
        ',GmbH',
        ' GMBH',
        ',GMBH',
        ' PT Pte\.ltd',
        ',PT Pte\.ltd',
        ' PTE',
        ',PTE',
        ' LLC',
        ',LLC',
        ' LLP',
        ',LLP',
        ' S\/C Ltda',
        ',S\/C Ltda',
        ' Ltda\.',
        ',Ltda\.',
        ' Pty Ltd\.',
        ',Pty Ltd\.',
        ' Pte Ltd\.',
        ',Pte Ltd\.',
        ' PTY\. LTD\.',
        ',PTY\. LTD\.',
        ' Pte Ltd',
        ',Pte Ltd',
        ' Ptv\. Ltd',
        ',Ptv\. Ltd',
        ' S\/C LTDA',
        ',S\/C LTDA',
        ' Company Ltd\.',
        ',Company Ltd\.',
        ' SA SOC LTD',
        ',SA SOC LTD',
        ' SOC LTD',
        ',SOC LTD',
        ' Ltd\.',
        ',Ltd\.',
        ' LTD',
        ',LTD',
        ' Ltd',
        ',Ltd',
        ' ltd',
        ',ltd',
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
        // スペースのみ 全角から半角に変換
        $name = str_replace('　', ' ', $name);

        // 会社名(英字)フィルター
        foreach($this->filterCharCompanyEn as $strCompanyEn){
            // フィルター文字が後方一致する場合は削除
            $name = preg_replace('/'.$strCompanyEn.'$/', '', $name, -1, $count);
            //一度置換を行った時点で終了
            if($count === 1){
                break;
            }
        }

        // 会社名フィルター
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
     * @param $isWebSearch
     * @return array
     * @throws Exception
     */
    public function searchCompany($companyId, $contractPlanId, $userId, $name, $city, $isFuzzy, $isWebSearch = false): array
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

            if ($isWebSearch) {

                if (mb_strlen($item) < 20) {
                    // 20文字未満の場合、完全一致での検索
                    //uniCaseName(inputNameのUniCase変換) = 検索文字(UniCase変換)
                    $query->whereRaw('uniCaseName = ?', [$this->convertToUniCase($item)]);
                } else {
                    // 20文字以上の場合、前方一致での検索
                    //uniCaseName(inputNameのUniCase変換) ? 検索文字(UniCase変換) . '%'
                    $query->whereRaw('uniCaseName like ?', $this->convertToUniCase($item) . '%');
                }

            } else {
                // WEB検索以外は、完全一致での検索
                //uniCaseName(inputNameのUniCase変換) = 検索文字(UniCase変換)
                $query->whereRaw('uniCaseName = ?', [$this->convertToUniCase($item)]);
            }

            if ($city !== '') {
                $query->where('address', 'like', $city . '%');
            }

            $query->orderByRaw('caseDate IS NULL DESC');
            $query->orderBy('caseDate', 'desc');

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
     * @param $isWebSearch
     * @return array
     * @throws Exception
     */
    public function searchPerson($companyId, $contractPlanId, $userId, $name, $age, $city, $isFuzzy, $birthday, $isWebSearch = false): array
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

            if ($isWebSearch) {

                if (mb_strlen($item) < 20) {
                    // 20文字未満の場合、完全一致での検索
                    $query->where(function($query) use($item) {
                        //uniCaseName(inputNameのUniCase変換) = 検索文字(UniCase変換)
                        $query->whereRaw('uniCaseName = ?', [$this->convertToUniCase($item)]);
                        //uniCaseKana(inputKanaのUniCase変換) = 検索文字(UniCase変換)
                        $query->orWhereRaw('uniCaseKana = ?', [$this->convertToUniCase($item)]);
                    });

                } else {
                    // 20文字以上の場合、前方一致での検索
                    $query->where(function($query) use($item) {
                        //uniCaseName(inputNameのUniCase変換) = 検索文字(UniCase変換)
                        $query->whereRaw('uniCaseName like ?', $this->convertToUniCase($item) . '%');
                        //uniCaseKana(inputKanaのUniCase変換) = 検索文字(UniCase変換)
                        $query->orWhereRaw('uniCaseKana like ?', $this->convertToUniCase($item) . '%');
                    });

                }

            } else {

                // WEB検索以外は、完全一致での検索
                $query->where(function($query) use($item) {
                    //uniCaseName(inputNameのUniCase変換) = 検索文字(UniCase変換)
                    $query->whereRaw('uniCaseName = ?', [$this->convertToUniCase($item)]);
                    //uniCaseKana(inputKanaのUniCase変換) = 検索文字(UniCase変換)
                    $query->orWhereRaw('uniCaseKana = ?', [$this->convertToUniCase($item)]);
                });

            }

            if ($age !== '') {
                $query->whereBetween('age', [$age - 1, $age + 1]);
            }

            if ($city !== '') {
                $query->where('address', 'like', $city . '%');
            }

            if ($birthday !== '') {
                $query->where('birthday', $birthday);
            }

            $query->orderByRaw('caseDate IS NULL DESC');
            $query->orderBy('caseDate', 'desc');

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
        $pdf->Text(10, 15, "Jcisデータベース 即時検索システム(Ver.3)",0.3, false, true, 0, 0, 'C');
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
