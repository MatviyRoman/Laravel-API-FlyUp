<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategoryTranslate extends Model
{
	protected $fillable = [
		'service_category_id',
		'language_id',
		'title',
		'seo_title',
		'keywords',
		'description',
		'url',
        'alt',
		'text',
		'subtext',
	];

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id')->select('id', 'name', 'flag');
	}

	public function serviceCategory()
	{
		return $this->belongsTo(ServiceCategory::class, 'service_category_id', 'id');
	}
}
