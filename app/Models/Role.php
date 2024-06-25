<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 * @property-read \Illuminate\Database\Eloquent\Collection|Ability[] $abilities
 * @package App\Models
 */
class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['id', 'role_name', 'role_text', 'role_group', 'group_text', 'text_color', 'back_color'];

    protected $hidden = [
        'created_at',
        'updated_at',
        'role_text',
        'group_text',
        'pivot',
        'hidden',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class,'user_roles','user_id','role_id');
    }

    public function abilities()
    {
        return $this->belongsToMany(Ability::class, 'role_abilities', 'role_id', 'ability_id');
    }
}
