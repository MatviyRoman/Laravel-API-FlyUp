<?php

namespace App\Http\Resources\Articles\User;

use App\ArticleTranslate;
use App\ArticleCategoryTranslate;
use App\ArticleAuthorTranslate;
use App\Http\Controllers\MainController;
use Illuminate\Http\Resources\Json\Resource;

class ArticleAll extends Resource
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
			'image' => $this->image,
            'alt' => $this->alt,
			'seo_image' => $this->seo_image,
			'likes' => $this->likes,
            'author' => ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
                ->where('language_id', $this->language_id)
                ->first() ?
                ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
                    ->where('language_id', $this->language_id)
                    ->select('article_author_id as id', 'name')
                    ->first() :
                ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
                    ->where('language_id', env('DEFAULT_LANG_ID', 1))
                    ->select('article_author_id as id', 'name')
                    ->first(),
			'views' => $this->views,
			'is_liked' => MainController::isLike('articles', $this->id),
			'created_at' => date('d/m/Y', strtotime($this->created_at)),
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
			'languages' => ArticleTranslate::where('article_id', $this->id)
				->join('articles', 'article_translates.article_id', '=', 'articles.id')
				->join('languages', 'article_translates.language_id', '=', 'languages.id')
				->orderBy('languages.id')
				->select('languages.id', 'languages.name', 'languages.flag', 'article_translates.url')
				->get(),
			'category_urls' => ArticleCategoryTranslate::where('article_category_id', $this->article_category_id)
				->select('language_id', 'url as category_url')
				->get(),
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
			'article_author' => ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
				->where('language_id', $this->language_id)
				->first() ?
				ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
					->where('language_id', $this->language_id)
					->select('article_author_id as id', 'name')
					->first() :
				ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
					->where('language_id', env('DEFAULT_LANG_ID', 1))
					->select('article_author_id as id', 'name')
					->first(),
		];
	}
}
