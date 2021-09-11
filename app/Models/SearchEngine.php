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
    private array $filterChar = [' ', '　', '・'];

    /**
     * 会社名専用フィルター文字
     * @var array|string[]
     */
    private array $filterCharCompany = [
        '-',
        "'",
        '/',
        '',
        '株式会社',
        '(株)',
        '㈱',
        '有限会社',
        '(有)',
        '㈲',
        '合資会社',
        '合同会社',
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
     * @param $name
     * @param $city
     * @param $isFuzzy
     * @return array
     */
    public function searchCompany($name, $city, $isFuzzy): array
    {

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
     * @param $name
     * @param $age
     * @param $city
     * @param $isFuzzy
     * @return array
     */
    public function searchPerson($name, $age, $city, $isFuzzy): array
    {

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
