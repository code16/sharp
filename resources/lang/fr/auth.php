<?php

return [
    'validation_error' => 'Veuillez renseigner les deux champs',
    'invalid_credentials' => 'Impossible de trouver un compte avec ces identifiants',
    '2fa' => [
        'validation_error' => 'Veuillez saisir le code',
        'invalid' => 'Ce code est invalide',
        'form_help_text' => 'Veuillez saisir les 6 chiffres du code de validation',
        'notification' => [
            'mail_subject' => 'Votre code de connexion',
            'mail_body' => 'Voici le code à saisir pour vous connecter :',
        ],
        'totp' => [
            'form_help_text' => 'Veuillez saisir les 6 chiffres du code de validation, ou d’un de vos codes de récupération non utilisé précédemment.',
            'commands' => [
                'activate' => [
                    'command_label' => 'Configurer l’authentification à deux facteurs',
                    'qrcode_field_label' => 'Scannez ce QR code avec votre application d’authentification',
                    'code_field_label' => 'Saisissez le code à 6 chiffres indiqué dans votre application',
                    'password_field_label' => 'Indiquez votre mot de passe',
                    'recovery_codes_field_label' => 'Codes de récupération',
                    'recovery_codes_field_help' => 'Conservez ces codes dans un endroit sûr. Ils vous permettront de vous connecter si vous perdez votre téléphone.',
                ],
                'deactivate' => [
                    'command_label' => 'Désactiver l’authentification à deux facteurs',
                ],
            ],
        ],
    ],
    'password_change' => [
        'command' => [
            'label' => 'Modifier le mot de passe...',
            'fields' => [
                'current_password' => 'Mot de passe actuel',
                'new_password' => 'Nouveau mot de passe',
                'new_password_confirm' => 'Confirmer le nouveau mot de passe',
            ],
        ],
    ],
];
