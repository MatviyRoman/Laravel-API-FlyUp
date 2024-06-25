<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'image',
        'phone',
        'email',
        'skype',
        'is_active',
        'order',
    ];

    public function translation()
    {
        return $this->hasMany(ContactTranslate::class, 'contact_id', 'id')
            ->where('language_id', env('DEFAULT_LANG_ID', 1))
            ->select(array('contact_id', 'contact_translates.name', 'contact_translates.position'));
    }

    public function languages()
    {
        return $this->belongsToMany('App\Language', 'contact_translates')->orderBy('order');
    }
}
