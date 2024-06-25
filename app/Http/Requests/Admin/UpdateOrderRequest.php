<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class UpdateOrderRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:orders,id',
            'service_id' => 'nullable|exists:services,id',
            'service_unit_id' => 'nullable|exists:service_units,id,deleted_at,NULL',
            'email' => 'nullable|max:255|email',
            'location' => 'nullable|max:100|string',
            'start' => 'nullable|date|date_format:Y-m-d H:i:s|before:end',
            'end' => 'nullable|date|date_format:Y-m-d H:i:s|after:start',
            'first_name' => 'nullable|max:100|string',
            'last_name' => 'nullable|max:100|string',
            'phone' => 'nullable|max:20|string',

            'type' => 'nullable|in:order,project',
            'admin_id' => 'nullable|exists:users,id',
            'user_id' => 'nullable|exists:users,id',
            'data' => 'nullable|json',
        ];
    }
}
