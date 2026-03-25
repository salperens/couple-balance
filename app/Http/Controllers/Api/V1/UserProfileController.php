<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UserProfile\CreateUserProfileAction;
use App\Actions\UserProfile\UpdateUserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserProfile\StoreUserProfileRequest;
use App\Http\Requests\Api\V1\UserProfile\UpdateUserProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        $profile = $request->user()->profile;

        return ProfileResource::make($profile);
    }

    public function store(StoreUserProfileRequest $request, CreateUserProfileAction $action): ProfileResource
    {
        $profile = $action->execute($request->user(), $request->toData());

        return ProfileResource::make($profile);
    }

    public function update(UpdateUserProfileRequest $request, UpdateUserProfileAction $action): ProfileResource
    {
        $profile = $action->execute($request->user(), $request->toData());

        return ProfileResource::make($profile);
    }
}
