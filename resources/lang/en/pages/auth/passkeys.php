<?php

return [
    'title' => 'Create a passkey',
    'name_field' => 'Name',
    'description' => '<p>Your device supports passkeys, a password replacement that validates your identity using touch, facial recognition, a device password, or a PIN.</p> <p>Passkeys can be used for sign-in as a simple and secure alternative to your password and two-factor credentials.</p>',
    'name_help_text' => 'The passkey name will help you identify it later.',
    'prompt_version' => [
        'button' => 'Create passkey',
        'skip_prompt_button' => 'Remind me later',
        'never_ask_again_button' => "Don't ask me again in this browser",
    ],
    'account_version' => [
        'button' => 'Create passkey',
        'cancel_button' => 'Cancel',
    ],
];
