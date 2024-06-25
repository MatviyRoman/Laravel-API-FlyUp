<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InterfaceGroup extends Model
{
    protected $fillable = [
        'page_id',
        'name',
        'title'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id', 'id');
    }
}
