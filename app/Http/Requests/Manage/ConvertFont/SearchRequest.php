<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\Manage\ConvertFont;

use App\Http\Requests\BaseRequest;

class SearchRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'targetCharacter' => ['max:1'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'targetCharacter' => '対象文字',
        ];
    }
}