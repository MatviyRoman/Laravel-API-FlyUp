<?php

namespace App\Http\Resources\Admin\Common;

use Illuminate\Http\Resources\Json\Resource;

class AbilityResource extends Resource
{
    public function toArray($request)
    {
        return [
            'ability_name' => $this->ability_name,
            'module' => $this->module,
            'ability_group' => $this->ability_group,
        ];
    }
}
