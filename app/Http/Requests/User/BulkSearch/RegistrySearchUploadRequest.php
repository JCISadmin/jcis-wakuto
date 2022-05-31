<?php

namespace App\Http\Requests\User\BulkSearch;

use App\Http\Requests\BaseRequest;

/**
 * Class RegistrySearchUploadRequest
 *   アップロード用
 *
 * @package App\Http\Requests\User\BulkSearch
 */
class RegistrySearchUploadRequest extends BaseRequest
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
    public function rules(): array
    {
        return [
            'bulk_file' => ['required', 'file', 'mimes:csv,txt,pdf,zip'],
        ];

    }

    /**
     * カスタムメッセージ定義
     *
     * @return array
     */
    public function messages()
    {
        return [
            'bulk_file.required' => 'アップロードファイルを設定してください。',
            'bulk_file.mimes' => 'ファイル形式が正しくありません。(CSV,PDF,ZIP)',
        ];
    }

    // ---------------------------------------------------------------- //
    // ----------------------- Methods protected ---------------------- //
    // ---------------------------------------------------------------- //

    // ---------------------------------------------------------------- //
    // ----------------------- Methods Private ------------------------ //
    // ---------------------------------------------------------------- //


}
