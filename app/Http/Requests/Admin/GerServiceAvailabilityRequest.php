<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class GerServiceAvailabilityRequest extends BaseApiRequest
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
            'start' => 'required|date|date_format:Y-m-d',
            'end' => 'required|date|date_format:Y-m-d',
        ];
    }
}
