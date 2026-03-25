<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\UserProfile\CreateUserProfileAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserProfile\StoreUserProfileRequest;
use App\Http\Requests\Api\V1\UserProfile\UpdateUserProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use Illuminate\Http\JsonResponse;
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

    public function update(UpdateUserProfileRequest $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json([
                'message' => 'Profile not found'
            ], 404);
        }

        $profile->update($request->validated());

        return response()->json([
            'data' => $profile
        ]);
    }
}
