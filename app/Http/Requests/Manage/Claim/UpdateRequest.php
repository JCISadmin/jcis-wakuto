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
            'claimDate' => ['required', 'date'],
            'paymentDate' => ['required', 'date'],
            'deliveryDate' => ['nullable', 'string', 'max:20'],
            'detail.expense.*.useFlg' => ['boolean'],
            'detail.expense.*.itemName' => ['nullable'],
            'detail.expense.*.amount' => ['nullable', 'integer', 'max:999999999', 'min:0'],
            'detail.expense.*.unit' => ['nullable', 'max:20'],
            'detail.expense.*.unitPrice' => ['nullable', 'integer', 'max:999999999', 'min:0'],
            'detail.expense.*.price' => ['nullable', 'integer', 'max:999999999', 'min:0'],
            'detail.expense.*.type' => ['nullable', 'max:20'],
            'detail.adjust.*.useFlg' => ['boolean'],
            'detail.adjust.*.itemName' => ['nullable'],
            'detail.adjust.*.amount' => ['nullable', 'integer', 'max:999999999', 'min:0'],
            'detail.adjust.*.unit' => ['nullable', 'max:20'],
            'detail.adjust.*.unitPrice' => ['nullable', 'integer', 'max:999999999', 'min:-999999999'],
            'detail.adjust.*.price' => ['nullable', 'integer', 'max:999999999', 'min:-999999999'],
            'detail.adjust.*.type' => ['nullable', 'max:20'],
            'claimNote' => ['nullable'],
            '*.deposit' => ['nullable', 'integer', 'max:999999999', 'min:0'],
            'memo' => ['nullable'],
        ];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            '*.deposit.max' => ':attributeは、:max以下で入力してください。',
            '*.deposit.min' => ':attributeは、:min以上で入力してください。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'claimDate' => '発行日',
            'paymentDate' => '支払期限',
            'deliveryDate' => '送付期限',
            'detail.expense.*.itemName' => '品番',
            'detail.expense.*.amount' => '数量',
            'detail.expense.*.unit' => '単位',
            'detail.expense.*.unitPrice' => '単価',
            'detail.expense.*.price' => '金額',
            'detail.adjust.*.itemName' => '請求補正理由',
            'detail.adjust.*.amount' => '請求補正数量',
            'detail.adjust.*.unit' => '請求補正単位',
            'detail.adjust.*.unitPrice' => '請求補正単価',
            'detail.adjust.*.price' => '請求補正金額',
            'claimNote' => '備考欄',
            '*.deposit' => 'デポジット残高',
            'memo' => 'メモ欄',
        ];
    }
}
