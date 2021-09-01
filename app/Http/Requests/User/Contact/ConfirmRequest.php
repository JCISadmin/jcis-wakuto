<?php
namespace App\Http\Requests\User\Contact;

use App\Http\Requests\BaseRequest;

/**
 * 確認バリデーション
 */
class ConfirmRequest extends BaseRequest
{

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'subject' => ['required'],
            'contactDetail' => ['required'],
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'subject' => '件名',
            'contactDetail' => 'お問い合わせ内容',
        ];
    }

}
