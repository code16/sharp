<?php

return [
    'validation_error' => 'Please enter a value for both fields',
    'invalid_credentials' => 'We could not find a user with these credentials',
    '2fa' => [
        'validation_error' => 'Please enter a value for the code',
        'invalid' => 'This code is invalid',
        'form_help_text' => 'Please enter the 6 figures of the validation code',
        'notification' => [
            'mail_subject' => 'Your connection code',
            'mail_body' => 'Here is the code to enter to connect:',
        ],
    ],
];
