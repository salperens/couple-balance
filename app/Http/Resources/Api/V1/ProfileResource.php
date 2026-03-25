<?php

namespace App\Http\Resources\Api\V1;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property UserProfile $resource
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'age'         => $this->resource->age,
            'gender'      => $this->resource->gender,
            'orientation' => $this->resource->orientation,
        ];
    }
}
