<?php

namespace Chaos\Majordomo\Http\Requests\Permission;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'alias' => 'required',
            'category' => 'required',
        ];
    }
}
