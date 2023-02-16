<?php

namespace Chaos\Majordomo\Http\Requests\Menu;

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
            'path' => 'required',
            'sequence' => 'required|numeric',
            'parent_id' => 'required|numeric',
        ];
    }
}
