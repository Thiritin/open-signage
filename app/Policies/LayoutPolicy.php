<?php

namespace App\Policies;

use App\Enums\ResourceOwnership;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LayoutPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Layout $layout): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Layout $layout): bool
    {
        if ($layout->project === null) {
            return true;
        }

        return $layout->project->type === ResourceOwnership::USER;
    }

    public function delete(User $user, Layout $layout): bool
    {
        if ($layout->project === null) {
            return true;
        }

        return $layout->project->type === ResourceOwnership::USER;
    }

    public function restore(User $user, Layout $layout): bool
    {
        if ($layout->project === null) {
            return true;
        }

        return $layout->project->type === ResourceOwnership::USER;
    }

    public function forceDelete(User $user, Layout $layout): bool
    {
        if ($layout->project === null) {
            return true;
        }

        return $layout->project->type === ResourceOwnership::USER;
    }
}
