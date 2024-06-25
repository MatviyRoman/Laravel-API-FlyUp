<?php

namespace App\Http\Requests\Admin\Common;

use App\Http\Requests\BaseApiRequest;

class CreateRoleRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'role_name' => 'required|max:255|unique:roles,role_name',
            'text_color' => 'nullable',
            'back_color' => 'nullable',
            'abilities' => 'required|array',
            'abilities.*' => 'nullable|exists:abilities,ability_name',
        ];
    }
}
