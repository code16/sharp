<?php

return [
    'validation_error' => 'Please enter a value for both fields',
    'invalid_credentials' => 'We could not find a user with these credentials',
    '2fa' => [
        'validation_error' => 'Please enter a value for the code',
        'invalid' => 'This code is invalid',
        'form_help_text' => 'Please enter the 6-digit validation code',
        'notification' => [
            'mail_subject' => 'Your connection code',
            'mail_body' => 'Here is the code to enter to connect:',
        ],
        'totp' => [
            'form_help_text' => 'Please enter the 6-digit validation code from your application, or one of your recovery codes that you have not used previously.',
            'commands' => [
                'activate' => [
                    'command_label' => 'Configure two-factor authentication',
                    'qrcode_field_label' => 'Scan this QR code with your authentication application',
                    'code_field_label' => 'Enter the 6-digit code from your application',
                    'password_field_label' => 'Enter your password',
                    'recovery_codes_field_label' => 'Recovery codes',
                    'recovery_codes_field_help' => 'Keep these codes in a safe place. They will allow you to connect if you lose your phone.',
                ],
                'deactivate' => [
                    'command_label' => 'Disable two-factor authentication',
                ],
            ],
        ],
    ],
    'password_change' => [
        'command' => [
            'label' => 'Change password...',
            'fields' => [
                'current_password' => 'Current password',
                'new_password' => 'New password',
                'new_password_confirm' => 'Confirm new password',
            ],
        ],
    ],
];
