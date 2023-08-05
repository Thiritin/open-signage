<?php
return [
    \App\Enums\EmergencyTypeEnum::FIRE->name => [
        'title' => 'Fire alarm',
        'message' => "A fire alarm has been triggered in the event area.\n Please leave the building immediately via the nearest emergency exit and go to the assembly point.",
    ],
    \App\Enums\EmergencyTypeEnum::EVACUATION->name => [
        'title' => 'Evacuation of the event area',
        'message' => "Due to a current situation, it is necessary to evacuate the event area.\n Please follow the instructions of the staff and leave the event area.",
    ],
    \App\Enums\EmergencyTypeEnum::TEST->name => [
        'title' => 'Alarm Systemtest',
        'message' => "This is a test alarm, no action is required.",
    ],
    \App\Enums\EmergencyTypeEnum::LIFTED->name => [
        'title' => 'All clear',
        'message' => "The situation is under control, there is no more danger.\n Please return to the event area.",
    ],
];
