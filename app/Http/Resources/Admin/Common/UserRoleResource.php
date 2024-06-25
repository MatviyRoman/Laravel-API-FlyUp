<?php

namespace App\Http\Resources\Admin\Common;

use Illuminate\Http\Resources\Json\Resource;

class UserRoleResource extends Resource
{
    public function toArray($request)
    {
        return [
            'role_name' => $this->role_name,
            'text_color' => $this->text_color,
            'back_color' => $this->back_color,
        ];
    }
}
