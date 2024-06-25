<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
	protected $fillable = [
		'is_active',
		'order',
		'image',
		'seo_image'
	];

	public function translation()
	{
		return $this->hasMany(ServiceCategoryTranslate::class, 'service_category_id', 'id')
			->where('language_id', env('DEFAULT_LANG_ID', 1))
			->select(array('service_category_id', 'title', 'url'));
	}

	public function languages()
	{
		return $this->belongsToMany(Language::class, 'service_category_translates')->orderBy('order')->select('name', 'flag');
	}
}
