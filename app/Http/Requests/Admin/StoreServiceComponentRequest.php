<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class StoreServiceComponentRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'nullable',
            'service_id' => 'required|exists:services,id',
            'name' => 'required|max:255',
            'notes' => 'nullable|max:255|string',
            'image' => 'nullable|max:255|string',
            'reg_number' => 'nullable|max:255|string',
            'price' => 'nullable|numeric|min:0',
            'work_start' => 'nullable|date|date_format:Y-m-d H:i:s',
            'work_end' => 'nullable|date|date_format:Y-m-d H:i:s',
            'repair' => 'nullable|string',
            'inspection' => 'nullable|string',
            'count' => 'nullable|numeric|min:0',
        ];
    }
}
