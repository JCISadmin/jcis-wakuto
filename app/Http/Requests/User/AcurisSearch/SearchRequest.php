<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\User\AcurisSearch;

use App\Http\Requests\BaseRequest;

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
            'dob' => ['nullable', 'date'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'dob.date' => ':attributeは、日付形式で入力してください。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'age' => '生年月日(Date of Birth)'
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
            $data['personName'][$key] = $item['forename'].$item['middleName'].$item['surname'];
            if(is_null($data['personName'][$key])){
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
            foreach ($data['companyName'] as $item) {
                if ($item !== '') {
                    return;
                }
            }

            foreach ($data['personName'] as $item) {
                if ($item !== '') {
                    return;
                }
            }

            $validator->errors()->add('companyName.0', "法人名または個人名を入力してください。");

        });
    }

}
