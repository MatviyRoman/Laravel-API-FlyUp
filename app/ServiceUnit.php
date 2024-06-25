<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceUnit extends Model
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at',
        'work_start',
        'work_end',
    ];

    protected $fillable = [
        'service_id',
        'location_id',
        'name',
        'notes',
        'image',
        'number',
        'price',
        'work_start',
        'work_end',
        'repair',
        'inspection',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }
}
