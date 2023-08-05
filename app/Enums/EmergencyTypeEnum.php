<?php

namespace App\Enums;

enum EmergencyTypeEnum
{
    case NONE;
    case FIRE;
    case EVACUATION;
    case CUSTOM;
    case TEST;
    case LIFTED;
}
