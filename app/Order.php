<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public const STATUS_NEW = 'new';
    const STATUS_PAYED = 'payed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_WORKING = 'working';
    const STATUS_DONE = 'done';
    const STATUS_ACCIDENT = 'accident';
    const STATUS_EXPIRED = 'expired';
    const STATUS_ARCHIVE = 'archive';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'admin_id',
        'service_id',
        'language_id',
        'service_unit_id',
        'start',
        'end',
        'first_name',
        'last_name',
        'email',
        'phone',
        'origin_price',
        'price',
        'discount_code',
        'type',
        'data',
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
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id')->select('id', 'name', 'flag');
    }

    /**
     * @return BelongsTo
     */
    public function serviceUnit(): BelongsTo
    {
        return $this->belongsTo(ServiceUnit::class, 'service_unit_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * @return mixed|string
     */
    public function calculateStatus()
    {
        $status = $this->status;

        if ($this->deleted_at) {
            $status = self::STATUS_ARCHIVE;
        } elseif ($status == self::STATUS_WORKING) {
            $now = Carbon::now();
            $orderEnd = Carbon::parse($this->end);

            if ($now->gte($orderEnd)) {
                $status = self::STATUS_EXPIRED;
            }
        }

        return $status;
    }
}
