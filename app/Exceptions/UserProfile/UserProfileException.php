<?php

declare(strict_types=1);

namespace App\Exceptions\UserProfile;

use Exception;

final class UserProfileException extends Exception
{
    public static function profileAlreadyExists(): self
    {
        return new self('Profile already exists');
    }
}
