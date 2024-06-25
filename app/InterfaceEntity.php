<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterfaceEntity extends Model
{
	protected $fillable = [
		'interface_group_id',
		'name',
        'title'
	];

	public function translations()
	{
		return $this->hasMany(InterfaceTranslate::class, 'enity_id', 'id');
	}

	public function interfaceGroup()
	{
		return $this->belongsTo(InterfaceGroup::class, 'interface_group_id', 'id');
	}

	public function languages()
	{
		return $this->belongsToMany(Language::class, 'interface_translates')->orderBy('order')->select('name', 'flag');
	}
}
