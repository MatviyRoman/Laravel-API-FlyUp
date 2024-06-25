<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleCategoryTranslate extends Model
{
	protected $fillable = [
		'article_category_id',
		'language_id',
		'title',
		'seo_title',
		'keywords',
		'description',
		'url',
	];

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}

	public function articleCategory()
	{
		return $this->belongsTo(ArticleCategory::class, 'article_category_id', 'id');
	}
}
