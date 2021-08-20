<?php

namespace App\Http\Requests\Manage\AdminUser;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;
use App\Rules\AlphaRule;

class UpdateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [
            'userInfo.*.userId' => ['required', 'max:20', 'regex:/^[!-~]+$/'],
            'userInfo.*.userName' => ['required', 'max:20'],
            'userInfo.*.mail' => ['required', 'email'],
            'userInfo.*.createDatetime' => ['required', 'date'],
        ];

        return $rules;
    }

    /**
     * カスタムメッセージ定義
     *
     * @return array
     */
    public function messages()
    {
        return [
            'userInfo.*.userId.required' => '管理者IDは、必須入力です。',
            'userInfo.*.userId.max' => '管理者IDは、:max文字までです。',
            'userInfo.*.userId.regex' => '管理者IDは、半角英数字で入力してください。',
            'userInfo.*.userName.required' => '管理者名は、必須入力です。',
            'userInfo.*.userName.max' => '管理者名は、:max文字までです。',
            'userInfo.*.mail.required' => '管理者E-mailは、必須入力です。',
            'userInfo.*.mail.email' => '管理者E-mailは、メールアドレスを入力してください。',
            'userInfo.*.createDatetime.required' => '登録日は、必須入力です。',
            'userInfo.*.createDatetime.date' => '登録日は、日付を入力してください。',
        ];

    }
}
