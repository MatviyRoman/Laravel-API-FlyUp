<?php

namespace App\Http\Resources\Vacancies\Admin;

use App\Http\Resources\Language;
use App\Vacancy as VacancyModel;
use Illuminate\Http\Resources\Json\Resource;

class Vacancy extends Resource
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
		    'languages' => Language::collection(VacancyModel::find($this->id)->languages),
	    ];
    }
}
