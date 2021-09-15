<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

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

    ];

    /**
     * 会社名専用フィルター文字
     * @var array|string[]
     */
    private array $filterCharCompany = [
        '合同会社', '（同）', '(同)',
        '医療法人', '医療法人社団', '医療法人財団', '社会医療法人', '（医）', '(医)',
        '財団法人', '（財）', '(財)',
        '一般財団法人', '（一財）', '(一財)',
        '公益財団法人', '（公財）', '(公財)',
        '社団法人', '（社）', '(社)',
        '一般社団法人', '（一社）', '(一社)',
        '公益社団法人', '（公社）', '(公社)',
        '宗教法人', '（宗）', '(宗)',
        '学校法人', '（学）', '(学)',
        '社会福祉法人', '（福）', '(福)',
        '更生保護法人',
        '相互会社', '（相）', '(相)',
        '特定非営利活動法人', '（特非）', '(特非)',
        '独立行政法人', '（独）', '(独)',
        '地方独立行政法人', '（地独）', '(地独)',
        '弁護士法人', '（弁）', '(弁)',
        '有限責任中間法人', '無限責任中間法人', '（中）', '(中)',
        '行政書士法人', '（行）', '(行)',
        '司法書士法人', '（司）', '(司)',
        '税理士法人', '（税）', '(税)',
        '国立大学法人', '公立大学法人', '（大）', '(大)',
        '農事組合法人',
        '管理組合法人',
        '社会保険労務士法人',
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
     */
    public function searchCompany($companyId, $contractPlanId, $userId, $name, $city, $isFuzzy): array
    {

        $keywordModel = new TKeywordHistory();
        $keywordModel->ins($companyId, $contractPlanId, $userId, hash('md5', $name));

        $nameList[] = $name;
        if ($isFuzzy) {
            $nameList = $this->convertFont($name);
        }

        $list = [];
        foreach ($nameList as $item) {
            $query = DB::table('mCorporation');
            $query->where('inputName', $item);

            if ($city !== '') {
                $query->where('address', 'like', $city . '%');
            }

            $retList = $query->get();

            foreach ($retList as $retItem) {
                $list[] = (array)$retItem;
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
     */
    public function searchPerson($companyId, $contractPlanId, $userId, $name, $age, $city, $isFuzzy, $birthday): array
    {

        $keywordModel = new TKeywordHistory();
        $keywordModel->ins($companyId, $contractPlanId, $userId, hash('md5', $name));

        $nameList[] = $name;
        if ($isFuzzy) {
            $nameList = $this->convertFont($name);
        }

        $list = [];
        foreach ($nameList as $item) {
            $inQuery = DB::table('mPerson');
            $inQuery->select(
                'mPerson.*',
                DB::raw("CASE caseAge WHEN null THEN null ELSE caseAge + TIMESTAMPDIFF(YEAR, mPerson.caseDate, CURRENT_DATE()) END as age")
            );

            /* @var string $inQuery */
            $query = DB::table($inQuery);

            $query->where(function($query) use($item) {
                $query->where('inputName', $item)->orWhere('inputKana', $item);
            });

            if ($age !== '') {
                $query->where('age', $age);
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

}
