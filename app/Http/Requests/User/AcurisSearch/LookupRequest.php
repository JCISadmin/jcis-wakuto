<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\User\AcurisSearch;

use App\Http\Requests\BaseRequest;

/**
 * 詳細検索バリデーション
 */
class LookupRequest extends BaseRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'resourceId' => ['required'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'resourceId.required' => ':attributeを選択してください。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'resourceId' => '詳細検索対象'
        ];
    }


}
