<?php

namespace App\Http\Resources\Vacancies\User;

use App\VacancyTranslate;
use App\Http\Controllers\MainController;
use Illuminate\Http\Resources\Json\Resource;

class VacancyAll extends Resource
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
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
		];
	}


	/**
	 * Get additional data that should be returned with the resource array.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return array
	 */
	public function with($request)
	{
		return [
			'languages' => VacancyTranslate::where('vacancy_id', $this->id)
				->join('vacancies', 'vacancy_translates.vacancy_id', '=', 'vacancies.id')
				->join('languages', 'vacancy_translates.language_id', '=', 'languages.id')
				->orderBy('languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'vacancy_translates.url')
				->get(),
		];
	}
}