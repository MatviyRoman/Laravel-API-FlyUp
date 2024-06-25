<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientTranslate extends Model
{
    protected $fillable = [
        'client_id',
        'language_id',
        'name',
        'alt'
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
