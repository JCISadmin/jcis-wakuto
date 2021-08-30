<?php

namespace App\Http\Requests\Manage\DataEdit;

use App\Http\Requests\BaseRequest;

class UpdateCorporationRequest extends BaseRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'inputName' => ['required', 'max:80'],
            'dispName' => ['required', 'max:80'],
            'industry' => ['nullable','max:60'],
            'postCode' => ['nullable', 'digits:7', 'numeric'],
            'address' => ['nullable', 'max:200'],
            'corporateCode' => ['nullable', 'max:20'],
            'tel' => ['nullable', 'max:20'],
            'requireDivision' => ['required', 'max:50'],
            'businessOwner' => ['nullable', 'max:20'],
            'department' => ['nullable', 'max:50'],
            'delegate' => ['nullable', 'max:20'],
            'casePersonName' => ['required', 'max:20'],
            'caseDate' => ['required', 'date_format:Y-m-d'],
            'caseSummary' => ['required'],
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
            'postCode.digits' => ':attributeは、:digits文字で入力してください。'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'inputName' => '法人・団体名(入力用)',
            'dispName' => '法人・団体名(表示用)',
            'industry' => '業種',
            'postCode' => '当時郵便番号',
            'address' => '当時団体所在地',
            'corporateCode' => '法人番号',
            'tel' => '所在地の電話番号',
            'requireDivision' => '要件区分',
            'businessOwner' => '当時実質経営者',
            'department' => '当時経営者所属',
            'delegate' => '当時代表者',
            'casePersonName' => '事案個人名',
            'caseDate' => '事案年月日',
            'caseSummary' => '事案概要',
            'disposalOffice' => '処分官署',
            'infoKind' => '情報種別',
            'infoSource' => '情報ソース',
            'filename' => 'ファイル名',
            'regDate' => '登録日',
            'note' => '備考',
        ];
    }


}

