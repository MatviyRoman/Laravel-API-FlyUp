<?php

namespace App\Http\Resources;

use App\Language;
use App\Article;
use App\ArticleCategoryTranslate;
use App\ArticleAuthorTranslate;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Facades\DB;

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
		    'id' => $this->article->id,
		    'url' => $this->url,
		    'title' => $this->title,
		    'seo_title' => $this->seo_title,
            'alt' => $this->alt,
		    'keywords' => $this->keywords,
		    'description' => $this->description,
		    'text' => $this->text,
		    'subtext' => $this->subtext,
		    'image' => $this->article->image,
		    'seo_image' => $this->article->seo_image,
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
			'article_category' => ArticleCategoryTranslate::where('article_category_id', $this->article->article_category_id)
				->where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_category_id as id', 'title')
				->first(),
			'article_categories' => DB::table('article_categories')
				->where('article_categories.is_last', true)
				->join('article_category_translates', 'article_categories.id', '=', 'article_category_translates.article_category_id')
				->where('article_category_translates.language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_categories.id', 'article_category_translates.title')
				->get(),
			'article_author' => ArticleAuthorTranslate::where('article_author_id', $this->article->article_author_id)
				->where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_author_id as id', 'name')
				->first(),
			'article_authors' => ArticleAuthorTranslate::where('language_id', env('DEFAULT_LANG_ID', 1))
				->select('article_author_id as id', 'name')
				->get(),
		];
	}
}
