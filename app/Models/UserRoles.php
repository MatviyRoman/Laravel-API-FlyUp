<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Common\UserRoles
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $role_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Common\Roles $role
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Common\UserRoles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Common\UserRoles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Common\UserRoles whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Common\UserRoles whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Common\UserRoles whereUserId($value)
 * @mixin \Eloquent
 */
class UserRoles extends Model
{
    protected $table = 'user_roles';

    protected $fillable = ['user_id','role_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_key', 'user_id','role_id');
    }

    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
