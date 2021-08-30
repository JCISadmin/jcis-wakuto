<?php

namespace App\Http\Requests\Manage\AdminUser;

use App\Http\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateRequest extends BaseRequest
{

    /**
     * @return array
     */
    #[ArrayShape(['userInfo.*.userId' => "string[]", 'userInfo.*.userName' => "string[]", 'userInfo.*.mail' => "string[]", 'userInfo.*.createDatetime' => "string[]", 'addUserId.*' => "string[]", 'addUserName.*' => "string[]", 'addMail.*' => "string[]"])] public function rules(): array
    {

        return [
            'userInfo.*.userId' => ['required', 'max:20', 'regex:/^[!-~]+$/'],
            'userInfo.*.userName' => ['required', 'max:20'],
            'userInfo.*.mail' => ['required', 'email'],
            'userInfo.*.createDatetime' => ['required', 'date'],
            'addUserId.*' => ['required', 'max:20', 'regex:/^[!-~]+$/'],
            'addUserName.*' => ['required', 'max:20'],
            'addMail.*' => ['required', 'email'],
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
            'userInfo.*.userId.required' => '管理者IDは、必須入力です。',
            'userInfo.*.userId.max' => '管理者IDは、:max文字までです。',
            'userInfo.*.userId.regex' => '管理者IDは、半角英数字で入力してください。',
            'userInfo.*.userName.required' => '管理者名は、必須入力です。',
            'userInfo.*.userName.max' => '管理者名は、:max文字までです。',
            'userInfo.*.mail.required' => '管理者E-mailは、必須入力です。',
            'userInfo.*.mail.email' => '管理者E-mailは、メールアドレスを入力してください。',
            'userInfo.*.createDatetime.required' => '登録日は、必須入力です。',
            'userInfo.*.createDatetime.date' => '登録日は、日付を入力してください。',

            'addUserId.*.required' => '管理者IDは、必須入力です。',
            'addUserId.*.max' => '管理者IDは、:max文字までです。',
            'addUserId.*.regex' => '管理者IDは、半角英数字で入力してください。',
            'addUserName.*.required' => '管理者名は、必須入力です。',
            'addUserName.*.max' => '管理者名は、:max文字までです。',
            'addMail.*.required' => '管理者E-mailは、必須入力です。',
            'addMail.*.email' => '管理者E-mailは、メールアドレスを入力してください。',

        ];

    }
}
