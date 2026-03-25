<?php

declare(strict_types=1);

namespace App\Data\UserProfile;

use App\Enums\GenderEnum;
use App\Enums\OrientationEnum;
use Spatie\LaravelData\Data;

final class UserProfileData extends Data
{
    public function __construct(
        public readonly ?int             $age,
        public readonly ?GenderEnum      $gender,
        public readonly ?OrientationEnum $orientation,
    )
    {
    }
}
