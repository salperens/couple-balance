<?php

declare(strict_types=1);

namespace App\Actions\UserProfile;

use App\Data\UserProfile\UserProfileData;
use App\Exceptions\UserProfile\UserProfileException;
use App\Models\User;
use App\Models\UserProfile;

final class UpdateUserProfileAction
{
    /**
     * @throws UserProfileException
     */
    public function execute(User $user, UserProfileData $data): UserProfile
    {
        $profile = $user->profile;

        if (!$profile) {
            throw UserProfileException::profileNotFoundForUser();
        }

        $updateData = array_filter($data->toArray(), static fn ($value) => $value !== null);

        $profile->update($updateData);

        $profile->fresh();

        return $profile;
    }
}
