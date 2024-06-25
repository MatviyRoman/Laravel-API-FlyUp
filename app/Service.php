<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
	protected $fillable = [
		'is_active',
		'order',
		'image',
		'seo_image',
		'icon',
		'docs',
		'price',
		'price2',
		'price3',
		'views',
		'likes',
        'article_author_id',
        'service_category_id',
        'is_delivery_required',
        'is_no_price',
	];

    protected $casts = [
        'price' => 'double',
        'is_delivery_required' => 'bool',
        'is_no_price' => 'bool',
        'docs' => 'json',
    ];

    protected static function booted()
    {
        static::deleting(function ($item) {
            $item->serviceControls()->sync([]);
            $item->serviceActions->each->delete();
        });
    }

	public function translation()
	{
		return $this->hasMany(ServiceTranslate::class, 'service_id', 'id')
			->where('language_id', env('DEFAULT_LANG_ID', 1))
			->select(array('service_id', 'title', 'url'));
	}

	public function languages()
	{
		return $this->belongsToMany(Language::class, 'service_translates')->orderBy('order')->select('name', 'flag');
	}

    /**
     * @return BelongsToMany
     */
    public function serviceControls(): BelongsToMany
    {
        return $this->belongsToMany(ServiceControl::class, 'service_control_services', 'service_id', 'service_control_id');
    }

    public function serviceActions(): HasMany
    {
        return $this->hasMany(ServiceAction::class, 'service_id');
    }
}
