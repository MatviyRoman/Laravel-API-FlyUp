<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleCategory extends Model
{
	protected $fillable = [
		'article_category_id',
		'is_active',
		'is_last',
		'has_articles',
		'order',
		'icon',
		'image',
		'seo_image',
	];

	public function translation()
	{
		return $this->hasMany(ArticleCategoryTranslate::class, 'article_category_id', 'id')
			->where('language_id', env('DEFAULT_LANG_ID', 1))
			->select(array('article_category_id', 'title', 'url'));
	}

	public function languages()
	{
		return $this->belongsToMany('App\Language', 'article_category_translates')->orderBy('order')->select('name', 'flag');
	}
}
