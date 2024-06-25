<?php

namespace App\Http\Requests\Online;

use App\Http\Requests\BaseApiRequest;

class GerServiceLocationsRequest extends BaseApiRequest
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
        ];
    }
}
