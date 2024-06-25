<?php

namespace App\Http\Resources\TextTranslations\Admin;

use App\Language;
use App\Seo;
use Illuminate\Http\Resources\Json\Resource;

class TextEntityAll extends Resource
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
			'id' => $this->text_entity->id,
			'name' => $this->text_entity->name,
			'value' => $this->value,
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
			'language' => Language::where('id', $this->language_id)
				->select('id', 'name', 'flag')
				->first(),
			'languages' => Language::orderBy('order')
				->select('id', 'name', 'flag')
				->get(),
			'page' => Seo::where('page_id', $this->text_entity->page_id)
				->where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('page_id as id', 'title')
				->first(),
			'pages' => Seo::where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('page_id as id', 'title')
				->get(),
		];
	}
}
