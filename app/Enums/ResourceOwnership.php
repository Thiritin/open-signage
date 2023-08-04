<?php

namespace App\Enums;

enum ResourceOwnership: string
{
    case USER = 'user';
    case SYSTEM = 'system';
    case EMERGENCY = 'emergency';
}
