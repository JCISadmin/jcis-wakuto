<?php

namespace App\Http\Requests\Manage\ConvertFont;

use App\Http\Requests\BaseRequest;
use App\Models\MConvertFont;

class UpdateRequest extends BaseRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'targetCharacter' => ['required', 'max:1'],
            'convertCharacter' => ['required'],
            'editId' => ['nullable'],
        ];

    }

    /**
     * カスタムメッセージ定義
     *
     * @return array
     */
    public function messages(): array
    {
        return [];

    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'targetCharacter' => '対象文字',
            'convertCharacter' => '変換字体',
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

            $convertCharacterAry = explode("\r\n", $data['convertCharacter']);
            $chkAry = [];
            foreach ($convertCharacterAry as $item) {
                if (array_key_exists($item, $chkAry)) {
                    $validator->errors()->add('convertCharacter', "変換字体に、重複文字があります。");
                    return;
                } else {
                    if (mb_strlen($item) != 1) {
                        $validator->errors()->add('convertCharacter', "変換字体は、1行1文字入力してください。");
                        return;
                    }

                    $chkAry[$item] = $item;
                }
            }

            if (count($chkAry) == 0) {
                $validator->errors()->add('convertCharacter', "変換字体に、有効な指定がありません。");
                return;
            }

            // 新規登録時のチェック
            if ($data['editId'] == '') {
                $model = new MConvertFont();
                $ret = $model->get($data['targetCharacter']);
                if (is_null($ret) == false) {
                    $validator->errors()->add('convertCharacter', "対象文字が、重複しています。");
                    return;
                }
            }

        });
    }
}
