<?php

return [
    \App\Enums\EmergencyTypeEnum::FIRE->name => [
        'title' => 'Feueralarm',
        'message' => "Im Veranstaltungsbereich wurde ein Feueralarm ausgelöst.\nBitte verlassen Sie das Gebäude umgehend über den nächsten Notausgang.",
    ],
    \App\Enums\EmergencyTypeEnum::EVACUATION->name => [
        'title' => 'Räumung der Veranstaltung',
        'message' => "Aufgrund einer aktuellen Situation ist es notwendig, den Veranstaltungsbereich zu räumen.\nBitte folgen Sie den Anweisungen des Personals und verlassen Sie den Veranstaltungsbereich.",
    ],
    \App\Enums\EmergencyTypeEnum::TEST->name => [
        'title' => 'Alarm Systemtest',
        'message' => 'Dies ist ein Testalarm, es ist keine Hanldung erforderlich.',
    ],
    \App\Enums\EmergencyTypeEnum::LIFTED->name => [
        'title' => 'Entwarnung',
        'message' => "Die Situation ist unter Kontrolle, es besteht keine Gefahr mehr.\nBitte kehren Sie in den Veranstaltungsbereich zurück.",
    ],
];
