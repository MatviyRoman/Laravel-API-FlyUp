<?php
/**
 * Created by PhpStorm.
 * User: doon
 * Date: 02.07.18
 * Time: 18:59
 */

namespace App\Http\Resources\Vacancies\Admin;

use Illuminate\Http\Resources\Json\Resource;
use App\Language;
use App\Vacancy;

class VacancyAll extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->vacancy_id,
            'name' => $this->name,
            'text' => $this->text,
            'url' => $this->url
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
//            'vacancies_number' => Vacancy::where('vacancy_id', $this->vacancy->id)->count(),
            'language' => Language::where('id', $this->language_id)->select('id', 'name', 'flag')->first(),
            'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get()
        ];
    }
}