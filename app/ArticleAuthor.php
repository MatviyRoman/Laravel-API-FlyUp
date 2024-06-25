<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleAuthor extends Model
{
	protected $fillable = [
		'is_active',
		'order',
	];

	public function translation()
	{
		return $this->hasMany(ArticleAuthorTranslate::class, 'article_author_id', 'id')
			->where('language_id', env('DEFAULT_LANG_ID', 1))
			->select(array('article_author_id', 'article_author_translates.name'));
	}

	public function languages()
	{
		return $this->belongsToMany('App\Language', 'article_author_translates')->orderBy('order');
	}
}
