<?php

namespace App\Http\Requests\Manage\User\SearchReport;

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
            'dispType' => ['required'],
            'useMonth' => ['nullable'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'dispType' => '表示方法',
            'useMonth' => '利用年月'
        ];
    }

}
