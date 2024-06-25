<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends BaseApiRequest
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
            'id' => 'required|exists:locations,id',
            'name' => [
                'required',
                'max:255',
                Rule::unique('locations')
                    ->ignore($id)
            ],
            'city' => 'nullable|max:255',
            'street' => 'nullable|max:255',
            'zip' => 'nullable|max:20',
            'user_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|max:255',
            'email' => 'nullable|max:255',
            'link' => 'nullable|max:255',
        ];
    }
}
