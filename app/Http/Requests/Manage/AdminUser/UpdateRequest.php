<?php

namespace App\Http\Requests\Manage\AdminUser;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\BaseRequest;

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
            'userInfo.*.userId' => ['required']
        ];

        return $rules;
    }
}
