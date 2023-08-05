<?php

namespace App\Policies;

use App\Enums\ResourceOwnership;
use App\Models\Playlist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaylistPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Playlist $playlist): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Playlist $playlist): bool
    {
        if ($playlist->project === null) {
            return true;
        }
        return $playlist->project->type === ResourceOwnership::USER;
    }

    public function delete(User $user, Playlist $playlist): bool
    {
        if ($playlist->project === null) {
            return true;
        }
        return $playlist->project->type === ResourceOwnership::USER;
    }

    public function restore(User $user, Playlist $playlist): bool
    {
    }

    public function forceDelete(User $user, Playlist $playlist): bool
    {
    }
}
