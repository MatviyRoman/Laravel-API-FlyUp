<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    const TYPE_PERCENT = 'percent';
    const TYPE_VALUE = 'value';

    protected $fillable = [
        'start',
        'end',
        'percent',
        'value',
        'type',
        'is_for_admin',
        'code',
    ];

    protected $hidden = [
        'updated_at',
    ];
}
