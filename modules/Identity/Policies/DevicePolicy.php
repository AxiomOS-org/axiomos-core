<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\Device;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class DevicePolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Device $device): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Device $device): bool
    {
        return true;
    }

    public function delete(Device $device): bool
    {
        return true;
    }
}
