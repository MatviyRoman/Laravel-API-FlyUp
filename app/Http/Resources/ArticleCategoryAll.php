<?php

namespace App\Http\Resources;

use App\ArticleCategoryTranslate;
use Illuminate\Http\Resources\Json\Resource;
use App\Language;
use App\Article;
use Illuminate\Support\Facades\DB;

class ArticleCategoryAll extends Resource
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
			'id' => $this->articleCategory->id,
			'url' => $this->url,
			'title' => $this->title,
			'seo_title' => $this->seo_title,
			'keywords' => $this->keywords,
			'description' => $this->description,
			'image' => $this->articleCategory->image,
			'seo_image' => $this->articleCategory->seo_image,
			'icon' => $this->articleCategory->icon
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
			'articles_number' => Article::where('article_category_id', $this->articleCategory->id)->count(),
			'language' => Language::where('id', $this->language_id)->select('id', 'name', 'flag')->first(),
			'languages' => Language::orderBy('order')->select('id', 'name', 'flag')->get(),
			'article_category' => ArticleCategoryTranslate::where('article_category_id', $this->articleCategory->article_category_id)
				->where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_category_id as id', 'title')
				->first(),
			'article_categories' => DB::table('article_categories')
				->where('article_categories.article_category_id', null)
				->where('article_categories.has_articles', false)
				->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
				->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_categories.id', 'article_category_translates.title')
				->get(),
		];
	}
}
