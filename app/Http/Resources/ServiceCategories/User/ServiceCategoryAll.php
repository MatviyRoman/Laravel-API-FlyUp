<?php

namespace App\Http\Resources\ServiceCategories\User;

use App\ServiceCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;

class ServiceCategoryAll extends Resource
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
			'title' => $this->title,
			'seo_title' => $this->seo_title,
			'keywords' => $this->keywords,
			'description' => $this->description,
			'text' => $this->text,
            'subtext' => $this->subtext,
            'image' => $this->image ? utf8_encode(file_get_contents($this->image)) : '',
			'seo_image' => $this->seo_image,
            'alt' => $this->alt
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
			'languages' => ServiceCategoryTranslate::where('service_category_id', $this->id)
				->join('service_categories', 'service_category_translates.service_category_id', '=', 'service_categories.id')
				->join('languages', 'service_category_translates.language_id', '=', 'languages.id')
				->orderBy('languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'service_category_translates.url')
				->get(),
		];
	}
}
