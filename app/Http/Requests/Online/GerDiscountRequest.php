<?php

namespace App\Http\Requests\Online;

use App\Http\Requests\BaseApiRequest;

class GerDiscountRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|exists:discounts',
        ];
    }
}
