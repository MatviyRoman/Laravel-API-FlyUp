<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class LocationResource extends Resource
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
            'user' => $this->user_id ? [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name' => $this->user->last_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'name' => $this->name,
            'city' => $this->city,
            'street' => $this->street,
            'zip' => $this->zip,
            'phone' => $this->phone,
            'email' => $this->email,
            'link' => $this->link,
        ];
    }
}
