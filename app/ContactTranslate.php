<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactTranslate extends Model
{
    protected $fillable = [
        'name',
        'position',
        'alt',
        'contact_id',
        'language_id'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }
}
