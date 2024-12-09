<?php

return [
    'validation_error' => 'Bitte füllen Sie beide Felder aus',
    'invalid_credentials' => 'Ein Nutzer mit diesen Zugangsdaten konnte nicht gefunden werden',
    '2fa' => [
        'validation_error' => 'Bitte geben Sie einen Wert für den Code ein',
        'invalid' => 'Dieser Code ist ungültig',
        'form_help_text' => 'Bitte geben Sie die 6 Ziffern des Validierungscodes ein',
        'notification' => [
            'mail_subject' => 'Ihr Verbindungscode',
            'mail_body' => 'Hier ist der Code, den Sie eingeben müssen, um sich zu verbinden:',
        ],
        'totp' => [
            'form_help_text' => 'Bitte geben Sie die 6 Ziffern des Validierungscodes ein oder einen Ihrer Wiederherstellungscodes ein, den Sie zuvor nicht verwendet haben.',
            'commands' => [
                'activate' => [
                    'command_label' => 'Zwei-Faktor-Authentifizierung konfigurieren',
                    'qrcode_field_label' => 'Scannen Sie diesen QR-Code mit Ihrer Authentifizierungsanwendung',
                    'code_field_label' => 'Geben Sie den 6-stelligen Code aus Ihrer Anwendung ein',
                    'password_field_label' => 'Geben Sie Ihr Passwort ein',
                    'recovery_codes_field_label' => 'Wiederherstellungscodes',
                    'recovery_codes_field_help' => 'Bewahren Sie diese Codes an einem sicheren Ort auf. Sie ermöglichen es Ihnen, sich zu verbinden, wenn Sie Ihr Telefon verlieren.',
                ],
                'deactivate' => [
                    'command_label' => 'Zwei-Faktor-Authentifizierung deaktivieren',
                ],
            ],
        ],
    ],
];
