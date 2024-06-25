<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    protected $table = 'abilities';

    protected $fillable = [
        'ability_name',
        'module',
        'ability_group',
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'ability_text',
        'pivot',
    ];
}
