<?php

namespace App\Http\Requests\Admin\Common;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateUserDataRequest extends BaseApiRequest
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
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')
                    ->ignore($id)
            ],

            'photo' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'language_id' => 'nullable|exists:languages,id',

            'dob' => 'nullable|date|date_format:Y-m-d',
            'type' => 'nullable|string|in:natural,legal',
            'files' => 'nullable|json',

            'users' => 'nullable|array',
            'users.*' => 'nullable|exists:users,id',
            'branches' => 'nullable|array',
            'branches.*' => 'nullable|exists:users,id',

            'data' => 'nullable|json',

            'role_id' => 'required|in:1,2',

            'image' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'ytunnus' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'e_address' => 'nullable|json',
            'gender' => 'nullable|string|max:10',
        ];
    }
}
