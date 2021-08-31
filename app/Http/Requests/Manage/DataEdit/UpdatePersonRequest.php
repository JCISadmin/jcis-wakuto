<?php

namespace App\Http\Requests\Manage\DataEdit;

use App\Http\Requests\BaseRequest;

class UpdatePersonRequest extends BaseRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'inputName' => ['required', 'max:130'],
            'dispName' => ['required', 'max:130'],
            'inputKana' => ['required', 'max:130'],
            'dispKana' => ['required', 'max:130'],
            'birthday' => ['required', 'date_format:Y-m-d'],
            'postCode' => ['nullable', 'digits:7', 'numeric'],
            'address' => ['nullable', 'max:200'],
            'requireDivision' => ['required', 'max:50'],
            'departmentJob' => ['nullable', 'max:50'],
            'department' => ['nullable', 'max:50'],
            'departmentAddress' => ['nullable', 'max:200'],
            'caseDate' => ['required', 'date_format:Y-m-d'],
            'caseSummary' => ['required'],
            'caseAge' => ['required','max:199','min:0','numeric'],
            'disposalOffice' => ['nullable', 'max:50'],
            'infoKind' => ['nullable', 'max:50'],
            'infoSource' => ['nullable', 'max:50'],
            'filename' => ['nullable', 'max:80'],
            'regDate' => ['required', 'date_format:Y-m-d'],
            'note' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'postCode.digits' => ':attributeは、:digits文字で入力してください。',
            'caseAge.max' => ':attributeは、:max以下で入力してください。',
            'caseAge.min' => ':attributeは、:max以上で入力してください。'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'inputName' => '氏名(入力用)',
            'dispName' => '氏名(表示用)',
            'inputKana' => '異名・かな(入力用)',
            'dispKana' => '異名・かな(表示用)',
            'birthday' => '生年月日',
            'postCode' => '当時郵便番号',
            'address' => '当時住所',
            'requireDivision' => '要件区分',
            'departmentJob' => '当時所属・役職',
            'department' => '当時所属団体名',
            'departmentAddress' => '当時所属所在地',
            'caseDate' => '事案年月日',
            'caseSummary' => '事案概要',
            'caseAge' => '当時年齢',
            'disposalOffice' => '処分官署',
            'infoKind' => '情報種別',
            'infoSource' => '情報ソース',
            'filename' => 'ファイル名',
            'regDate' => '登録日',
            'note' => '備考',
        ];
    }


}

