<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\Manage\Claim;

use App\Http\Requests\BaseRequest;

class SearchRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'claimMonth' => ['required', 'date_format:Y-m'],
            'companyName' => ['max:20'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'claimMonth' => '請求月',
            'companyName' => '会社名',
        ];
    }
}
