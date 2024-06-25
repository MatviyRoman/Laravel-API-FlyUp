<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class StoreServiceActionRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_control_id' => 'required|exists:service_controls,id',
            'service_id' => 'required|exists:services,id',

            'result' => 'nullable|max:255',
            'sum' => 'nullable|regex:/^\d{0,10}(\.\d{1,2})?$/',
            'notes' => 'nullable',
            'data' => 'nullable|json',
        ];
    }
}
