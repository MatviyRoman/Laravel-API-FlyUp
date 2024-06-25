<?php

namespace App\Http\Resources\TextTranslations\Admin;

use App\TextEntity as TextEntityModel;
use App\Http\Resources\Language;
use App\Seo;
use Illuminate\Http\Resources\Json\Resource;

class TextEntity extends Resource
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
			'title' => $this->title,
			'value' => $this->value,
//			'page' => Seo::where('page_id', $this->page_id)
//				->where('language_id', env('DEFAULT_LANG_ID', 1))
//				->select('page_id as id', 'title')
//				->first(),
//			'languages' => Language::collection(TextEntityModel::find($this->id)->languages),
		];
	}
}
