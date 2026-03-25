<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\UserProfile\StoreUserProfileRequest;
use App\Http\Requests\Api\V1\UserProfile\UpdateUserProfileRequest;
use App\Http\Resources\Api\V1\ProfileResource;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function show(Request $request): ProfileResource
    {
        $profile = $request->user()->profile;

        return ProfileResource::make($profile);
    }

    public function store(StoreUserProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->profile) {
            return response()->json([
                'message' => 'Profile already exists'
            ], 422);
        }

        $profile = UserProfile::create([
            'user_id' => $user->id,
            ...$request->validated()
        ]);

        return response()->json([
            'data' => $profile
        ], 201);
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
