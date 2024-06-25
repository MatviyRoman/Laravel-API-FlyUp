<?php

namespace App\Models;

use App\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceComponent extends Model
{
    protected $table = 'service_components';

    protected $fillable = [
        'service_id',
        'name',
        'notes',
        'image',
        'reg_number',
        'price',
        'work_start',
        'work_end',
        'repair',
        'inspection',
        'count',
    ];

    protected $casts = [
        'work_start' => 'datetime',
        'work_end' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
