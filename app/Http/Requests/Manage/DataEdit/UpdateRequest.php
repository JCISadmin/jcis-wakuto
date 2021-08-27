<?php

namespace App\Http\Requests\Manage\DataEdit;

use App\Http\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    /**
     *
     * @return array
     */

    /**
     * @return array
     */
    #[ArrayShape(['userInfo.*.userId' => "string[]", 'userInfo.*.userName' => "string[]", 'userInfo.*.mail' => "string[]", 'userInfo.*.createDatetime' => "string[]", 'addUserId.*' => "string[]", 'addUserName.*' => "string[]", 'addMail.*' => "string[]"])] public function rules(): array
    {

        return [
        ];

    }

    /**
     * カスタムメッセージ定義
     *
     * @return array
     */
    public function messages(): array
    {
        return [
        ];

    }
}
