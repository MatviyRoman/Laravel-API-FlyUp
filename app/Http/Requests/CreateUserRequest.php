<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CreateUserRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'nullable|date|date_format:Y-m-d',
            'photo' => 'nullable|string',
            'phone' => 'nullable|string|max:20',

            'type' => 'nullable|string|in:natural,legal',
            'files' => 'nullable|json',
            'data' => 'nullable|json',

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')
                    ->whereNotNull('password')
            ],
            'language_id' => 'required||exists:languages,id',

            'image' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'ytunnus' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|string|max:10',

            'send_email' => 'nullable|boolean',
        ];
    }
}
