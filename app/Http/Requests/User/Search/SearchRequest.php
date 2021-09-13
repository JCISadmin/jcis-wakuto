<?php

namespace App\Http\Requests\User\Search;

use App\Http\Requests\BaseRequest;
use App\Models\SearchEngine;

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
            'age' => ['nullable', 'max:199', 'min:0', 'numeric'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'age.max' => ':attributeは、:max以下で入力してください。',
            'age.min' => ':attributeは、:min以上で入力してください。'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'age' => '絞り込み(現年齢)'
        ];
    }


    /**
     * 検索名称のトリム処理
     */
    protected function prepareForValidation()
    {
        $model = new SearchEngine();

        $data = $this->all();
        foreach ($data['companyName'] as $key => $item) {
            $data['companyName'][$key] = $model->filterCompany($item);
        }

        foreach ($data['parsonName'] as $key => $item) {
            $data['parsonName'][$key] = $model->filterPerson($item);
        }

        $this->replace($data);

    }


    /**
     * 独自バリデーション
     *
     * @param $validator
     */
    public function withValidator($validator) {
        $validator->after(function ($validator) {

            if (count($validator->failed()) > 0) {
                // エラーありの場合は、抜ける
                return;
            }

            $data = $this->input();
            foreach ($data['companyName'] as $item) {
                if ($item !== '') {
                    return;
                }
            }

            foreach ($data['parsonName'] as $item) {
                if ($item !== '') {
                    return;
                }
            }

            $validator->errors()->add('companyName.0', "法人名または個人名を入力してください。");

        });
    }

}
