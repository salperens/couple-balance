<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Enums\OrientationEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $age
 * @property GenderEnum|null $gender
 * @property OrientationEnum|null $orientation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'age',
        'gender',
        'orientation',
    ];

    protected $casts = [
        'gender' => GenderEnum::class,
        'orientation' => OrientationEnum::class,
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
