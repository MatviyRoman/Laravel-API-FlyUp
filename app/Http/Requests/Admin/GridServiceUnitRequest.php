<?php

namespace App\Http\Requests\Admin;

use VklComponents\VklTable\VklTableRequest;

class GridServiceUnitRequest extends VklTableRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function additionalRules()
    {
        return [
            'service_id' => 'nullable|exists:services,id'
        ];
    }
}