<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseApiRequest;
use App\Order;
use Illuminate\Validation\Rule;

class ChangeOrderStatusRequest extends BaseApiRequest
{
    const AVAILABLE_STATUSES = [
        Order::STATUS_NEW,
        Order::STATUS_PAYED,
        Order::STATUS_PROCESSING,
        Order::STATUS_WORKING,
        Order::STATUS_DONE,
        Order::STATUS_ACCIDENT,
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|exists:orders,id',
            'status' => [
                'required',
                Rule::in(self::AVAILABLE_STATUSES),
            ],
        ];
    }
}
