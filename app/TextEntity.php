<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TextEntity extends Model
{
	protected $fillable = [
		'interface_group_id',
		'name',
        'title'
	];

	public function translations()
	{
		return $this->hasMany(TextTranslate::class, 'enity_id', 'id');
	}

    public function interfaceGroup()
    {
        return $this->belongsTo(InterfaceGroup::class, 'interface_group_id', 'id');
    }

	public function languages()
	{
		return $this->belongsToMany(Language::class, 'text_translates')->orderBy('order')->select('name', 'flag');
	}
}
