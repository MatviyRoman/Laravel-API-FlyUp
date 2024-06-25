<?php

namespace App\Http\Resources\Admin\Common;

use Illuminate\Http\Resources\Json\Resource;

class RoleResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'role_name' => $this->role_name,
            'text_color' => $this->text_color,
            'back_color' => $this->back_color,
            'abilities' => AbilityResource::collection($this->abilities),
        ];
    }
}
