<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Data\Auth\DeviceData;
use App\Models\Device;

final class FindOrCreateDeviceAction
{
    public function execute(int $userId, DeviceData $deviceData): Device
    {
        $device = Device::query()
            ->where('user_id', $userId)
            ->where('device_id', $deviceData->deviceId)
            ->first();

        if ($device) {
            $device->update([
                'os'          => $deviceData->os,
                'model'       => $deviceData->model,
                'app_version' => $deviceData->appVersion,
                'push_token'  => $deviceData->pushToken,
            ]);

            return $device;
        }

        return Device::query()->create([
            'user_id'     => $userId,
            'device_id'   => $deviceData->deviceId,
            'os'          => $deviceData->os,
            'model'       => $deviceData->model,
            'app_version' => $deviceData->appVersion,
            'push_token'  => $deviceData->pushToken,
        ]);
    }
}
