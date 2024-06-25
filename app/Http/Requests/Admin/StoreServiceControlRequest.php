<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;

class StoreServiceControlRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'required|exists:services,id',
            'name' => 'required|max:255',
            'type' => 'required|max:20',
            'data' => 'nullable|json',
        ];
    }
}
