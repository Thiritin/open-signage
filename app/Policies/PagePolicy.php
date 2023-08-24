<?php

namespace App\Policies;

use App\Enums\ResourceOwnership;
use App\Models\Page;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Page $page): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Page $page): bool
    {
        if ($page->project === null) {
            return true;
        }

        return $page->project->type === ResourceOwnership::USER;
    }

    public function delete(User $user, Page $page): bool
    {
        if ($page->project === null) {
            return true;
        }

        return $page->project->type === ResourceOwnership::USER;
    }

    public function restore(User $user, Page $page): bool
    {
        return true;
    }

    public function forceDelete(User $user, Page $page): bool
    {
        return true;
    }
}
