<?php

namespace App\Http\Requests\Manage\User;

use App\Http\Requests\BaseRequest;
use App\Models\MUserCompany;

class UpdateRequest extends BaseRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'userCompany.chargeName' => ['nullable','max:20'],
            'userCompany.chargeMail' => ['required','email'],
            'userCompany.name' => ['nullable','max:40'],
            'userCompany.kana' => ['nullable','max:40'],
            'userCompany.companyId' => ['required','regex:/^[!-~]+$/','max:5'],
            'userCompany.postCode' => ['nullable', 'digits:7', 'numeric'],
            'userCompany.address' => ['nullable','max:50'],
            'userCompany.tel' => ['nullable','regex:/^[0-9-]+$/','max:20'],
            'userCompany.staffName' => ['nullable','max:20'],
            'userCompany.staffDepartmentJob' => ['nullable','max:100'],
            'userCompany.staffTel' => ['nullable','regex:/^[0-9-]+$/','max:20'],
            'userCompany.staffMail' => ['nullable','email'],
            'userCompany.claimNam' => ['nullable','max:20'],
            'userCompany.claimDepartmentJob' => ['nullable','max:100'],
            'userCompany.claimTel' => ['nullable','regex:/^[0-9-]+$/','max:20'],
            'userCompany.paymentTerm' => ['nullable','numeric'],
            'userCompany.deliveryDate' => ['nullable','max:20'],
            'ipAddress.*' => ['required','regex:/^((25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])$/'],
            '*.startTrial' => ['nullable','date'],
            '*.useStartDate' => ['nullable','date'],
            '*.useUpdateDate' => ['nullable','date'],
            '*.useEndAlertDate' => ['nullable','date'],
            '*.useEndDate' => ['nullable','date'],
            '*.idUnitPrice' => ['nullable','numeric','max:999999999','min:0'],
            '*.searchUnitPrice' => ['nullable','numeric','max:9999','min:0'],
            '*.searchCount' => ['nullable','integer'],
            '*.deposit' => ['nullable','numeric','max:999999999','min:0'],
            '*.trialSearchUnitPrice' => ['nullable','numeric','max:9999','min:0'],
            '*.userDetail.*.name' => ['required','max:20'],
            'addWebName.*' => ['required','max:20'],
            'addApiName.*' => ['required','max:20'],
            '*.userDetail.*.departmentJob' => ['nullable','max:100'],
            'addWebDepartmentJob.*' => ['nullable','max:100'],
            'addApiDepartmentJob.*' => ['nullable','max:100'],
            '*.userDetail.*.mail' => ['required','email'],
            'addWebDepartmentJobMail.*' => ['required','email'],
            'addApiDepartmentJobMail.*' => ['required','email'],
            'contractStartDate' => ['nullable','date'],
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
            'userCompany.companyId.regex' => ':attributeは、半角英数字で入力してください。',
            'userCompany.tel.regex' => ':attributeは、電話番号を入力してください。',
            'userCompany.staffTel.regex' => ':attributeは、電話番号を入力してください。',
            'userCompany.claimTel.regex' => ':attributeは、電話番号を入力してください。',
            'ipAddress.*.regex' => ':attributeが無効な形式です。',
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
            'userCompany.chargeName' => '当社窓口',
            'userCompany.chargeMail' => '当社窓口Email',
            'userCompany.name' => '会社名',
            'userCompany.kana' => '会社名フリガナ',
            'userCompany.companyId' => '会社ID',
            'userCompany.postCode' => '郵便番号',
            'userCompany.address' => '会社住所',
            'userCompany.tel' => '代表電話番号',
            'userCompany.staffName' => '担当者名',
            'userCompany.staffDepartmentJob' => '担当者部署・役職',
            'userCompany.staffTel' => '担当者電話番号',
            'userCompany.staffMail' => '担当者E-Mail',
            'userCompany.claimNam' => '請求者名',
            'userCompany.claimDepartmentJob' => '請求者部署・役職',
            'userCompany.claimTel' => '請求者電話番号',
            'userCompany.paymentTerm' => '支払期限',
            'userCompany.deliveryDate' => '送付期限',
            'ipAddress.*' => 'IPアドレス',
            '*.startTrial' => 'トライアル開始日',
            '*.useStartDate' => '利用開始日',
            '*.useUpdateDate' => '利用更新日',
            '*.useEndAlertDate' => '利用終了通知日',
            '*.useEndDate' => '利用終了予定日',
            '*.idUnitPrice' => 'ID代',
            '*.searchUnitPrice' => '検索単価',
            '*.searchCount' => '年検索数',
            '*.deposit' => 'デポジット残高',
            '*.userDetail.*.name' => 'ID保有者名',
            'addWebName.*' => 'ID保有者名',
            'addApiName.*' => 'ID保有者名',
            '*.userDetail.*.departmentJob' => 'ID保有者部署・役職',
            'addWebDepartmentJob.*' => 'ID保有者部署・役職',
            'addApiDepartmentJob.*' => 'ID保有者部署・役職',
            '*.userDetail.*.mail' => 'ID保有者E-mail',
            'addWebDepartmentJobMail.*' => 'ID保有者E-mail',
            'addApiDepartmentJobMail.*' => 'ID保有者E-mail',
            'contractStartDate' => '契約更新日',
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

            // 新規登録時のチェック
            if ($data['editId'] == '') {
                // 重複チェック
                $model = new MUserCompany();
                $ret = $model->get($data['userCompany']['companyId']);
                if ($ret['userCompany'] !== []) {
                    $validator->errors()->add('userCompany.companyId', "会社IDが、重複しています。");
                    return;
                }
            }

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

            if(is_null($data['userCompany']['claimMailBcc']) === false){
                $mailAry = explode(",", $data['userCompany']['claimMailBcc']);
                foreach ($mailAry as $item) {
                    if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                        $validator->errors()->add('userCompany.claimMailBcc', "請求先BCCは、メールアドレスを入力してください。");
                    }
                }
            }

            if(isset($data['web']['userDetail'])){
                foreach($data['web']['userDetail'] as $list){
                    if(is_null($list['idMailBcc']) === false){
                        $mailAry = explode(",", $list['idMailBcc']);
                        foreach ($mailAry as $item) {
                            if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                                $validator->errors()->add('*.userDetail.*.idMailBcc', "ID通知先BCCは、メールアドレスを入力してください。");
                            }
                        }
                    }
                }
            }

            if(isset($data['addWebDepartmentJobidMailBcc'])){
                foreach($data['addWebDepartmentJobidMailBcc'] as $list){
                    if(is_null($list) === false){
                        $mailAry = explode(",", $list);
                        foreach ($mailAry as $item) {
                            if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                                $validator->errors()->add('addWebDepartmentJobidMailBcc', "ID通知先BCCは、メールアドレスを入力してください。");
                            }
                        }
                    }
                }
            }

            if(isset($data['api']['userDetail'])){
                foreach($data['api']['userDetail'] as $list){
                    if(is_null($list['idMailBcc']) === false){
                        $mailAry = explode(",", $list['idMailBcc']);
                        foreach ($mailAry as $item) {
                            if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                                $validator->errors()->add('*.userDetail.*.idMailBcc', "ID通知先BCCは、メールアドレスを入力してください。");
                            }
                        }
                    }
                }
            }

            if(isset($data['addApiDepartmentJobidMailBcc'])){
                foreach($data['addApiDepartmentJobidMailBcc'] as $list){
                    if(is_null($list) === false){
                        $mailAry = explode(",", $list);
                        foreach ($mailAry as $item) {
                            if(filter_var($item, FILTER_VALIDATE_EMAIL) === false){
                                $validator->errors()->add('addApiDepartmentJobidMailBcc', "ID通知先BCCは、メールアドレスを入力してください。");
                            }
                        }
                    }
                }
            }

            $webIds = $data['web']['ids'];
            $apiIds = $data['api']['ids'];
            If(array_key_exists('addWebDelFlg',$data)){
                foreach($data['addWebDelFlg'] as $value){
                    if((int)$value === 0){
                        $webIds++;
                    }
                }
            }
            if($webIds > 999){
                $validator->errors()->add('web.ids', "登録できる有効なID個数は、3桁までです。");
            }

            If(array_key_exists('addApiDelFlg',$data)){
                foreach($data as $value){
                    if((int)$value === 0){
                        $apiIds++;
                    }
                }
            }
            if($apiIds > 999){
                $validator->errors()->add('api.ids', "登録できる有効なID個数は、3桁までです。");
            }

            if(!isset($data['ipAddress'])){
                $data['ipAddress'] = [];
            }
            if(count($data['ipAddress']) > 999){
                $validator->errors()->add('ipAddress', "登録できる有効な許可IPアドレスは、3桁までです。");
            }
            $this->replace($data);

        });
    }
}
