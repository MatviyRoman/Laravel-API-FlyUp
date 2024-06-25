<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Validation\Rule;

class UpdateDiscountRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = optional($this->validationData())['id'];

        return [
            'id' => 'required|exists:discounts',
            'code' => [
                'required',
                'min:3',
                'max:50',
                'string',
                Rule::unique('discounts')
                    ->ignore($id)
            ],
            'start' => 'nullable|date|date_format:Y-m-d H:i:s|before:end',
            'end' => 'nullable|date|date_format:Y-m-d H:i:s|after:start',
            'percent' => 'nullable|integer|min:1|max:100|required_without:value',
            'value' => 'nullable|numeric|min:0.01|required_without:percent',
            'is_for_admin' => 'required|boolean',
            'type' => 'required|in:percent,value',
        ];
    }
}
