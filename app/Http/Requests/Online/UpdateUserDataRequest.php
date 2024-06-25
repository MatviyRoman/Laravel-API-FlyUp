<?php

namespace App\Http\Requests\Online;

use App\Http\Requests\BaseApiRequest;

class UpdateUserDataRequest extends BaseApiRequest
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

            'image' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person_phone' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
            'ytunnus' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|string|max:10',

            'phone' => 'nullable|string|max:20',
            'language_id' => 'nullable||exists:languages,id',
        ];
    }
}
