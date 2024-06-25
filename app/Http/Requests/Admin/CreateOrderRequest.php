<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class CreateOrderRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = optional($this->validationData())['user_id'];

        $rules = [
            'service_id' => 'required|exists:services,id',
            'location_id' => 'required|exists:locations,id',
            'language_id' => 'required|exists:languages,id',
            'start' => 'required|date|date_format:Y-m-d H:i:s|before:end',
            'end' => 'required|date|date_format:Y-m-d H:i:s|after:start',

            'discount_code' => 'nullable|exists:discounts,code',

            'type' => 'nullable|in:order,project',
            'admin_id' => 'nullable|exists:users,id',
            'data' => 'nullable|json',
        ];

        if (!$userId) {
            $rules = array_merge($rules, [
                'email' => 'required|max:255|email',
                'first_name' => 'required|max:100|string',
                'last_name' => 'required|max:100|string',
                'phone' => 'required|max:20|string',
            ]);
        } else {
            $rules = array_merge($rules, [
                'user_id' => 'required|exists:users,id',
            ]);
        }

        return $rules;
    }
}
