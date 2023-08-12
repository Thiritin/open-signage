<?php

namespace App\Enums;

enum ScreenStatusEnum: string
{
    case UNINITIALIZED = "uninitialized";
    case ONLINE = "online";
    case OFFLINE = "offline";
    case RESTARTING = "restarting";
}
