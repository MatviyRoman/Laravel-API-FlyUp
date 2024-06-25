<?php

namespace App\Http\Requests\Admin;

use App\Discount;
use App\Http\Requests\BaseApiRequest;

class CreateDiscountRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|max:50|min:3|string|unique:discounts',
            'start' => 'nullable|date|date_format:Y-m-d H:i:s|before:end',
            'end' => 'nullable|date|date_format:Y-m-d H:i:s|after:start',
            'percent' => 'nullable|integer|min:1|max:100|required_without:value',
            'value' => 'nullable|numeric|min:0.01|required_without:percent',
            'is_for_admin' => 'required|boolean',
            'type' => 'required|in:'. Discount::TYPE_PERCENT .','. Discount::TYPE_VALUE,
        ];
    }
}
