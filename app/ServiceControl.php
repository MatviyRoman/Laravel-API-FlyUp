<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceControl extends Model
{
    protected $table = 'service_controls';

    protected $fillable = [
        'name',
        'type',
        'data',
    ];

    protected static function booted()
    {
        static::deleting(function ($item) {
            $item->services()->sync([]);
            $item->serviceActions->each->delete();
        });
    }

    /**
     * @return BelongsToMany
     */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'service_control_services', 'service_control_id', 'service_id');
    }

    public function serviceActions(): HasMany
    {
        return $this->hasMany(ServiceAction::class, 'service_control_id');
    }
}
