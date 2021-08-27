<?php

namespace App\Http\Requests\Manage\ConvertFont;

use App\Http\Requests\BaseRequest;
use JetBrains\PhpStorm\ArrayShape;

class UpdateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    /**
     *
     * @return array
     */

    /**
     * @return array
     */
    public function rules(): array
    {

        return [
            'updateTargetCharacter' => ['required'],
            'updateConvertCharacter' => ['required'],
        ];

        if ($this->input('updateFlg') === false) {
            array_unshift($rules['updateTargetCharacter'], 'unique:mConvertFont,targetCharacter');
        }

    }

    /**
     * カスタムメッセージ定義
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'updateTargetCharacter.required' => '対象文字は、必須入力です。',
            'updateTargetCharacter.unique' => '対象文字が、重複しています。',
            'updateConvertCharacter.required' => '変換字体は、必須入力です。',
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

            $targetCharacter = $this->input('updateTargetCharacter');
            $convertCharacter = $this->input('updateConvertCharacter');
            $items = explode("\r\n", $convertCharacter);
            $count = 0;
            foreach($items as $item){
                //変換字体の文字列長チェック
                if(mb_strlen($item) != 1){
                    $validator->errors()->add('updateTargetCharacter', "変換字体は、1行につき1文字までです。");
                    return;
                }

                //対象文字と変換字体の重複チェック
                if($targetCharacter === $item){
                    $validator->errors()->add('updateTargetCharacter', "対象文字が、変換字体と重複しています。");
                    return;
                }

                //変換字体の重複チェック
                $workItem = $item;
                foreach($items as $item){
                    if($workItem === $item){
                        $count += 1;
                    }    
                }
                if($count > 1){
                    $validator->errors()->add('updateTargetCharacter', "変換字体が、重複しています。");
                    return;
                }
            }
        });
    }
}
