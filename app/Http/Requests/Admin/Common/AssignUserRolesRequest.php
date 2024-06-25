<?php

namespace App\Http\Requests\Admin\Common;

use App\Http\Requests\BaseApiRequest;

class AssignUserRolesRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'nullable|exists:roles,role_name',
        ];
    }
}
