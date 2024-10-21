<?php

return [
    'validation_error' => 'Por favor, introduce un valor para ambos campos.',
    'invalid_credentials' => 'No podemos encontrar un usuario con esos credenciales',
    'login' => [
        'page_title' => 'Iniciar sesión',
        'login_field' => 'login',
        'password_field' => 'contraseña',
        'code_field' => 'código',
        'remember' => 'Mantener sesión activa',
        'button' => 'login',
    ],
    'reset_password' => [
        'page_title' => 'Restablecer contraseña',
    ],
    'forgot_password' => [
        'page_title' => '¿Has olvidado tu contraseña?',
    ],
    'status' => [
        'passwords' => [
            'reset' => '¡Tu contraseña ha sido restablecida!',
            'sent' => '¡Te hemos enviado por correo electrónico el enlace para restablecer tu contraseña! (Si un usuario está asociado a esta dirección de correo electrónico)',
            'throttled' => 'Por favor, espera antes de volver a intentarlo.',
            'token' => 'Este token de restablecimiento de contraseña no es válido.',
            'user' => 'No podemos encontrar un usuario con esa dirección de correo electrónico.',
        ],
    ],
    '2fa' => [
        'validation_error' => 'Introduce un valor para el código.',
        'invalid' => 'Este código no es válido.',
        'form_help_text' => 'Por favor, introduce los 6 dígitos del código de validación.',
        'notification' => [
            'mail_subject' => 'Tu código de conexión',
            'mail_body' => 'Este es el código que debes introducir para conectarte:',
        ],
        'totp' => [
            'form_help_text' => 'Por favor, introduce los 6 dígitos del código de validación, o uno de tus códigos de recuperación que no hayas utilizado previamente.',
            'commands' => [
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
    ],
];
