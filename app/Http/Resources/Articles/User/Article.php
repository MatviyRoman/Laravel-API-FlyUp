<?php

namespace App\Http\Resources\Articles\User;

use App\Http\Resources\Language;
use Illuminate\Http\Resources\Json\Resource;
use App\ArticleCategoryTranslate;

class Article extends Resource
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
			'image' => $this->image,
			'views' => $this->views,
			'likes' => $this->likes,
			'name' => $this->title,
			'subtext' => $this->subtext,
			'url' => $this->url,
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
			'article_category' => ArticleCategoryTranslate::where('article_category_id', $this->article_category_id)
				->where('language_id', $this->language_id)
				->first() ?
				ArticleCategoryTranslate::where('article_category_id', $this->article_category_id)
				->where('language_id', $this->language_id)
				->select('article_category_id as id', 'title', 'url')
				->first() :
				ArticleCategoryTranslate::where('article_category_id', $this->article_category_id)
				->where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_category_id as id', 'title', 'url')
				->first(),
		];
	}
}
