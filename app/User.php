<?php

namespace App;

use App\Models\Role;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
  	use HasApiTokens, Notifiable;

    const STATUS_AUTH = 'authorized';
    const STATUS_NOT_COMPLETED = 'not_completed'; // auth but not completed personal info
    const STATUS_INVITED = 'invited';
    const STATUS_BLOCKED = 'blocked';
    const STATUS_RESTORE = 'restore';

    protected $casts = [
        'users' => 'json',
        'branches' => 'json',
        'files' => 'json',
        'data' => 'json',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'blocked',
        'email',
        'phone',
        'password',
        'language_id',
        'role_id',
        'gender',
        'address',
        'e_address',
        'company_name',
        'image',
        'zip',
        'ytunnus',
        'contact_person_phone',
        'contact_person_email',
        'contact_person_name',
        'verification_token',
        'remember_token',

        'dob',
        'files',
        'users',
        'branches',
        'data',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'blocked',
        'updated_at',
        'password',
        'remember_token',
        'verification_token',
    ];

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    public function has_role($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->roles->keyBy('role_name')->has($role)) {
                    return true;
                }
            }
            return false;
        }
        return $this->roles->keyBy('role_name')->has($roles);
    }

    public function getStatusAttribute()
    {
        if ($this->blocked) {
            return self::STATUS_BLOCKED;
        }

        if ($this->remember_token) {
            return self::STATUS_RESTORE;
        }

        if ($this->password) {
            if ($this->phone) {
                return self::STATUS_AUTH;
            } else {
                return self::STATUS_NOT_COMPLETED;
            }
        }

        return self::STATUS_INVITED;
    }
}