<?php

namespace App\Events;

use App\Enums\EmergencyTypeEnum;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Support\Collection;

class EmergencyEvent
{
    use Dispatchable;

    public function __construct(
        public readonly User $user,
        public readonly EmergencyTypeEnum $type,
        public readonly Collection $screens,
        public readonly ?string $message = null,
        public readonly ?string $title = null)
    {

    }
}
