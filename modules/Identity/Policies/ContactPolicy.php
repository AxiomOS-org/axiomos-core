<?php

declare(strict_types=1);

namespace Modules\Identity\Policies;

use Modules\Identity\Domain\Models\Contact;

/**
 * Allows all actions until 5C authorization rollout.
 */
final class ContactPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(Contact $contact): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(Contact $contact): bool
    {
        return true;
    }

    public function delete(Contact $contact): bool
    {
        return true;
    }
}
