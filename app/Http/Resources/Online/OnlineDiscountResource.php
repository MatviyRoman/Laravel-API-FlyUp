<?php

namespace App\Http\Resources\Online;

use Illuminate\Http\Resources\Json\Resource;

class OnlineDiscountResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'code' => $this->code,
            'percent' => $this->percent,
            'value' => $this->value,
            'type' => $this->type,
        ];
    }
}
