<?php

namespace App\Http\Requests\Manage\User;

use App\Http\Requests\BaseRequest;

class UpdateRequest extends BaseRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'userCompany.contractStatus' => ['required'],
            'userCompany.chargeMail' => ['nullable','email'],
            'userCompany.companyId' => ['required','regex:/^[!-~]+$/'],
            'userCompany.postCode' => ['nullable', 'digits:7', 'integer'],
            'userCompany.staffMail' => ['nullable','email'],
            //'userCompany.tel' => ['max:999'],
            'web.contractPlanId' => ['required'],
            'api.contractPlanId' => ['required'],
            'web.contractTypeId' => ['required'],
            'api.contractTypeId' => ['required'],
            '*.startTrial' => ['nullable','date'],
            '*.useStartDate' => ['nullable','date'],
            '*.useUpdateDate' => ['nullable','date'],
            '*.useEndAlertDate' => ['nullable','date'],
            '*.useEndDate' => ['nullable','date'],
            '*.ids' => ['max:999'],
            '*.idUnitPrice' => ['nullable','integer'],
            '*.searchUnitPrice' => ['nullable','integer'],
            '*.searchCount' => ['nullable','integer'],
            '*.deposit' => ['nullable','integer'],
            '*.userDetail.*.name' => ['required'],
            '*.userDetail.*.mail' => ['required','email'],
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
            'companyId.regex' => ':attributeは、半角英数字で入力してください。',
            'postCode.digits' => ':attributeは、:digits文字で入力してください。',
            '*.ids.max' => '登録できる:attributeは、:max個までです。',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'userCompany.contractStatus' => '状況',
            'userCompany.chargeMail' => '当社窓口Email',
            'userCompany.companyId' => '会社名ID',
            'userCompany.postCode' => '郵便番号',
            'userCompany.staffMail' => '担当者E-Mail',
            'web.contractPlanId' => '契約プラン',
            'api.contractPlanId' => '契約プラン',            
            'web.contractTypeId' => '契約形態',
            'api.contractTypeId' => '契約形態',
            '*.startTrial' => 'トライアル開始日',
            '*.useStartDate' => '利用開始日',
            '*.useUpdateDate' => '利用更新日',
            '*.useEndAlertDate' => '利用終了通知日',
            '*.useEndDate' => '利用終了予定日',
            '*.ids' => 'ID個数',
            '*.idUnitPrice' => 'ID代',
            '*.searchUnitPrice' => '検索単価',
            '*.searchCount' => '年検索数',
            '*.deposit' => 'デポジット残高',
            '*.userDetail.*.name' => 'ID保有者名',
            '*.userDetail.*.mail' => 'ID保有者E-mail',            
        ];
    }

    /**
     * After Hook 追加バリデーション
     *
     * @param $validator
     */
    public function withValidator($validator) {
        $validator->after(function ($validator) {

            if (count($validator->failed()) > 0) {
                // エラーありの場合は、抜ける
                return;
            }

            $data = $this->input();

            //複数メールアドレスのチェック
            if(is_null($data['userCompany']['claimMailTo']) === false){
                $mailAry = explode(",", $data['userCompany']['claimMailTo']);
                foreach ($mailAry as $item) {
                    if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                        $validator->errors()->add('userCompany.claimMailTo', "請求先TOは、メールアドレスを入力してください。");
                    }
                }
            }

            if(is_null($data['userCompany']['claimMailCc']) === false){
                $mailAry = explode(",", $data['userCompany']['claimMailCc']);
                foreach ($mailAry as $item) {
                    if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                        $validator->errors()->add('userCompany.claimMailCc', "請求先CCは、メールアドレスを入力してください。");
                    }
                }
            }

        });
    }
}
