<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
	protected $fillable = [
		'name',
		'email',
		'type',
        'file',
		'message',
		'comment',
        'phone',
		'language_id',
		'service_id',
		'is_viewed',
	];

	public function service()
	{
		return $this->belongsTo(Service::class, 'service_id', 'id');
	}

	public function language()
	{
		return $this->belongsTo(Language::class, 'language_id', 'id');
	}
}
