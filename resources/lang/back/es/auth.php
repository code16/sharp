<?php

return [
    'validation_error' => 'Por favor, introduce un valor para ambos campos.',
    'invalid_credentials' => 'No podemos encontrar un usuario con esos credenciales',
    '2fa' => [
        'validation_error' => 'Introduce un valor para el código.',
        'invalid' => 'Este código no es válido.',
        'form_help_text' => 'Por favor, introduce los 6 dígitos del código de validación.',
        'notification' => [
            'mail_subject' => 'Tu código de conexión',
            'mail_body' => 'Este es el código que debes introducir para conectarte:',
        ],
        'totp_commands' => [
            'activate' => [
                'command_label' => 'Configurar la autenticación de dos factores',
                'qrcode_field_label' => 'Escanea este código QR con tu aplicación de autenticación',
                'code_field_label' => 'Introduce el código de 6 dígitos de tu aplicación',
                'password_field_label' => 'Introduce tu contraseña',
                'recovery_codes_field_label' => 'Códigos de recuperación',
                'recovery_codes_field_help' => 'Guarda estos códigos en un lugar seguro. Te permitirán conectarte si pierdes tu teléfono.',
            ],
            'deactivate' => [
                'command_label' => 'Desactivar la autenticación de dos factores',
            ],
        ],
    ],
];
