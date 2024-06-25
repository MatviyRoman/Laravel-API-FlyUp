<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceAction extends Model
{
    protected $table = 'service_actions';

    protected $fillable = [
        'service_control_id',
        'service_id',
        'result',
        'sum',
        'notes',
        'data',
    ];

    /**
     * @return BelongsTo
     */
    public function serviceControl(): BelongsTo
    {
        return $this->belongsTo(ServiceControl::class, 'service_control_id');
    }

    /**
     * @return BelongsTo
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
