<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;
use App\Article as ArticleModel;
use App\ArticleCategoryTranslate;
use App\ArticleAuthorTranslate;

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
		    'is_active' => $this->is_active,
		    'order' => $this->order,
		    'image' => $this->image,
		    'views' => $this->views,
		    'likes' => $this->likes,
		    'name' => $this->title,
		    'url' => $this->url,
		    'article_category' => ArticleCategoryTranslate::where('article_category_id', $this->article_category_id)
			    ->where('language_id', env('DEFAULT_LANG_ID', 1))
			    ->select('article_category_id as id', 'title')
			    ->first(),
		    'article_author' => ArticleAuthorTranslate::where('article_author_id', $this->article_author_id)
			    ->where('language_id', env('DEFAULT_LANG_ID', 1))
			    ->select('article_author_id as id', 'name')
			    ->first(),
		    'languages' => Language::collection(ArticleModel::find($this->id)->languages),
	    ];
    }
}
