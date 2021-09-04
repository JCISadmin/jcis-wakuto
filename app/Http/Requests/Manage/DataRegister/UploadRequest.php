<?php

namespace App\Http\Requests\Manage\DataRegister;

use App\Http\Requests\BaseRequest;

/**
 * Class UploadRequest
 *   アップロード用
 *
 * @package App\Http\Requests\Manage\DataRegister
 */
class UploadRequest extends BaseRequest
{

    // ---------------------------------------------------------------- //
    // ----------------------- Class Variables ------------------------ //
    // ---------------------------------------------------------------- //

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Public ------------------------- //
    // ---------------------------------------------------------------- //

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() :bool
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
            'csv_file' => ['required', 'file', 'mimes:tsv,txt'],
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
            'csv_file.required' => 'アップロードファイルを設定してください。',
            'csv_file.mimes' => 'csvファイルを設定してください。',
        ];
    }

    // ---------------------------------------------------------------- //
    // ----------------------- Methods protected ---------------------- //
    // ---------------------------------------------------------------- //

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Private ------------------------ //
    // ---------------------------------------------------------------- //


}
