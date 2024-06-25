<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class CreateServiceUnitRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_id' => 'required|exists:services,id',
            'location_id' => 'required|exists:locations,id',
            'name' => 'required|max:255',
            'notes' => 'nullable|max:255|string',
            'image' => 'nullable|max:255|string',
            'number' => 'nullable|max:255|string',
            'price' => 'nullable|numeric',
            'work_start' => 'nullable|date|date_format:Y-m-d H:i:s',
            'work_end' => 'nullable|date|date_format:Y-m-d H:i:s',
            'repair' => 'nullable|string',
            'inspection' => 'nullable|string',
        ];
    }
}
