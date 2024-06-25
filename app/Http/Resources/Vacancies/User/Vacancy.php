<?php

namespace App\Http\Resources\Vacancies\User;

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
			'name' => $this->name,
			'text' => $this->text,
            'url' => $this->url,
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
		];
	}
}