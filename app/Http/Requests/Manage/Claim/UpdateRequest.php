<?php /** @noinspection PhpArrayShapeAttributeCanBeAddedInspection */

namespace App\Http\Requests\Manage\Claim;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{
    /**
     * @return array

     */
    public function rules(): array
    {

        return [
            'paymentDate' => ['required', 'date'],
            'adjustNote' => ['max:20'],
            'adjustPrice' => ['nullable', 'max:9999999999', 'integer'],
            'deposit.*' => ['nullable', 'integer', 'max:9999999999', 'min:0'],
            'memo' => ['nullable'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            'adjustPrice' => ':attributeは、:max以下で入力してください。',
            'deposit.*.max' => ':attributeは、:max以下で入力してください。',
            'deposit.*.min' => ':attributeは、:min以上で入力してください。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'paymentDate' => '支払期日',
            'adjustNote' => '請求補正理由',
            'adjustPrice' => '請求補正金額',
            'deposit.*' => 'デポジット残高',
            'memo' => 'メモ欄',
        ];
    }
}
