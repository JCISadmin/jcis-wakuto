<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\User\AcurisSearch;

use App\Http\Requests\BaseRequest;
use App\Models\AcurisSearch;

/**
 * 検索バリデーション
 */
class SearchRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'datasets' => ['required'],
            'dob' => ['nullable', 'date', 'after:1899/12/31', 'before:tomorrow'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'dob.date' => ':attributeは、日付形式で入力してください。',
            'dob.after' => ':attributeは、1900/01/01以降の日付を入力してください。',
            'dob.before' => ':attributeは、本日以前の日付を入力してください。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'datasets' => '検索条件',
            'dob' => '生年月日(Date of Birth)'
        ];
    }


    /**
     * 検索名称のトリム処理
     */
    protected function prepareForValidation()
    {
        $data = $this->all();

        foreach ($data['companyName'] as $key => $item) {
            if(is_null($item)){
                $data['companyName'][$key] = '';
            }
        }

        foreach ($data['personName'] as $key => $item) {
            $name = $item['forename'].$item['middleName'].$item['surname'];
            $data['personName'][$key] = $item['forename'].' '.$item['middleName'].' '.$item['surname'];
            if($name === ''){
                $data['personName'][$key] = '';
            }
        }

        $this->replace($data);

    }


    /**
     * 独自バリデーション
     *
     * @param $validator
     * @noinspection PhpUnused
     */
    public function withValidator($validator) {
        $validator->after(function ($validator) {

            if (count($validator->failed()) > 0) {
                // エラーありの場合は、抜ける
                return;
            }

            $data = $this->input();

            $isName = false;

            // 法人用の検索条件を除外
            $businessesDatasets = array_diff($data['datasets'], AcurisSearch::EXCLUDE_SEARCH_COND_BUSINESSES);
            // 個人用の検索条件を除外
            $individualsDatasets = array_diff($data['datasets'], AcurisSearch::EXCLUDE_SEARCH_COND_INDIVIDUALS);
            
            foreach ($data['companyName'] as $item) {
                if ($item !== '') {
                    // 法人の検索がある場合
                    if ($businessesDatasets === []) {
                        $validator->errors()->add('datasets', "法人名検索の有効な検索条件が設定されていません。");
                    }
                    $isName = true;
                }
            }

            foreach ($data['personName'] as $item) {
                if ($item !== '') {
                    // 個人の検索がある場合
                    if ($individualsDatasets === []) {
                        $validator->errors()->add('datasets', "個人名検索の有効な検索条件が設定されていません。");
                    }
                    $isName = true;
                }
            }

            if ($isName === false) {
                $validator->errors()->add('companyName.0', "法人名または個人名を入力してください。");
            }

        });
    }

}
