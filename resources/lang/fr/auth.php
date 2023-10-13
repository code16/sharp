<?php

return [
    'validation_error' => 'Veuillez renseigner les deux champs',
    'invalid_credentials' => 'Impossible de trouver un compte avec ces identifiants',
    'login' => [
        'page_title' => 'Connexion',
        'login_field' => 'identifiant',
        'password_field' => 'mot de passe',
        'code_field' => 'code',
        'remember' => 'Rester connecté',
        'button' => 'Connexion',
    ],
    'forgot_password' => [
        'page_title' => 'Mot de passe oublié',
    ],
    'reset_password' => [
        'page_title' => 'Réinitialiser le mot de passe',
    ],
    'status' => [
        'passwords' => [
            'reset' => 'Votre mot de passe a été réinitialisé !',
            'sent' => 'Nous vous avons envoyé par email le lien de réinitialisation de votre mot de passe ! (Si un utilisateur est associé à cette adresse email)',
            'throttled' => 'Veuillez patienter avant de réessayer.',
            'token' => 'Ce jeton de réinitialisation de mot de passe est invalide.',
            'user' => 'Impossible de trouver un compte avec cette adresse email.',
        ],
    ],
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
];
