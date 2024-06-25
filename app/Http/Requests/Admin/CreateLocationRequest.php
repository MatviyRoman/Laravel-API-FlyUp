<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class CreateLocationRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255|unique:locations,name',
            'city' => 'nullable|max:255',
            'street' => 'nullable|max:255',
            'phone' => 'nullable|max:255',
            'email' => 'nullable|max:255',
            'link' => 'nullable|max:255',
            'zip' => 'nullable|max:20',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}
