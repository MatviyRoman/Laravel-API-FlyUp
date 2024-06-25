<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Client as ClientModel;

class Client extends Resource
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
            'id' => $this->id,
            'is_active' => $this->is_active,
            'order' => $this->order,
            'name' => $this->name,
            'icon' => $this->icon,
            'languages' => Language::collection(ClientModel::where('id', $this->id)->with('languages')->first()->languages)
        ];
    }
}
