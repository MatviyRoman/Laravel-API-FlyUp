<?php

namespace App\Http\Resources;

use App\Article;
use App\ArticleCategory as ArticleCategoryModel;
use Illuminate\Http\Resources\Json\Resource;

class ArticleCategory extends Resource
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
		    'article_category_id' => $this->article_category_id,
		    'is_active' => $this->is_active,
		    'icon' => $this->icon,
		    'order' => $this->order,
		    'title' => $this->title,
		    'url' => $this->url,
		    'articles_number' => Article::where('article_category_id', $this->id)->count(),
		    'languages' => Language::collection(ArticleCategoryModel::where('id', $this->id)->with('languages')->first()->languages),
	    ];
    }
}
