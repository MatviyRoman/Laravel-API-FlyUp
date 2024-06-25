<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'icon',
        'is_active',
        'order',
    ];

    public function translation()
    {
        return $this->hasMany(ClientTranslate::class, 'client_id', 'id')
            ->where('language_id', env('DEFAULT_LANG_ID', 1))
            ->select(array('client_id', 'client_translates.name'));
    }

    public function languages()
    {
        return $this->belongsToMany('App\Language', 'client_translates')->orderBy('order');
    }
}
