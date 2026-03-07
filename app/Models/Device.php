<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property $id int
 * @property $user_id int
 * @property $device_id int
 * @property $os string
 * @property $model string
 * @property $app_version string
 * @property $push_token string
 */
class Device extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'os',
        'model',
        'app_version',
        'push_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
