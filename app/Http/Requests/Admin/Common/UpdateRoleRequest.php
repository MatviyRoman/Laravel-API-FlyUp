<?php

namespace App\Http\Requests\Admin\Common;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = optional($this->validationData())['id'];

        return [
            'id' => 'required',
            'role_name' => [
                'required',
                'max:255',
                Rule::unique('roles')
                    ->ignore($id)
            ],
            'text_color' => 'nullable',
            'back_color' => 'nullable',
            'abilities' => 'required|array',
            'abilities.*' => 'nullable|exists:abilities,ability_name',
        ];
    }
}
