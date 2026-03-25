<?php

declare(strict_types=1);

namespace App\Actions\UserProfile;

use App\Data\UserProfile\UserProfileData;
use App\Exceptions\UserProfile\UserProfileException;
use App\Models\User;
use App\Models\UserProfile;

final readonly class CreateUserProfileAction
{
    /**
     * @throws UserProfileException
     */
    public function execute(User $user, UserProfileData $profileData): UserProfile
    {
        if ($user->profile) {
            throw UserProfileException::profileAlreadyExists();
        }

        return UserProfile::query()->create(
            [
                'user_id'     => $user->id,
                'age'         => $profileData->age,
                'gender'      => $profileData->gender,
                'orientation' => $profileData->orientation,
            ]
        );
    }
}
