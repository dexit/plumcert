<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Certificate recurrence
    |--------------------------------------------------------------------------
    |
    | How many months after a certificate is issued the next compliance visit
    | should be auto-scheduled. A certificate may override this with its own
    | `recurrence_months` value (0 / null = do not recur).
    |
    */
    'cert_recurrence' => [
        'cp12_homeowner'         => 12,
        'cp12_landlord'          => 12,
        'gas_service_record'     => 12,
        'installation_checklist' => 12,
        'minor_works'            => 0,
        'warning_notice'         => 0,
        'disconnection'          => 0,
    ],

    // Selectable recurrence options shown in the certificate wizard.
    'recurrence_options' => [
        0  => 'No recurrence',
        6  => 'Every 6 months',
        12 => 'Annually (12 months)',
        24 => 'Every 2 years',
        60 => 'Every 5 years',
    ],

    /*
    |--------------------------------------------------------------------------
    | Reminder lead times
    |--------------------------------------------------------------------------
    |
    | Days before a due date at which staggered reminders fire.
    |
    */
    'reminder_lead_days' => [30, 7, 0],

    // Contact phone surfaced in templated reminders/certificates.
    'support_phone' => env('SUPPORT_PHONE', '0800 000 0000'),

];
