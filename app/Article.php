<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	protected $fillable = [
		'article_category_id',
		'article_author_id',
		'is_active',
		'order',
		'image',
		'seo_image',
		'views',
		'likes'
	];

	public function translation()
	{
		return $this->hasMany(ArticleTranslate::class, 'article_id', 'id')
			->where('language_id', env('DEFAULT_LANG_ID', 1))
			->select(array('article_id', 'title', 'url'));
	}

	public function languages()
	{
		return $this->belongsToMany(Language::class, 'article_translates')->orderBy('order')->select('name', 'flag');
	}
}
