<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

use Closure;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class Deactivate2faViaTotpCommand extends EntityCommand
{
    public function __construct(protected Sharp2faHandler $handler)
    {
    }

    public function label(): ?string
    {
        return trans('sharp::auth.2fa.totp_commands.deactivate.command_label');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setInputTypePassword()
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.password_field_label'))
            );
    }

    public function execute(array $data = []): array
    {
        $this->validate($data, [
            'password' => [
                'required',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    $loginAttr = config('sharp.auth.login_attribute', 'email');
                    $passwordAttr = config('sharp.auth.password_attribute', 'password');

                    $credentials = [
                        $loginAttr => auth()->user()->$loginAttr,
                        $passwordAttr => $value,
                    ];

                    if (! auth()->validate($credentials)) {
                        $fail(trans('sharp::auth.invalid_credentials'));
                    }
                },
            ],
        ]);

        $this->handler->setUser(auth()->user())->deactivate2faForUser();

        return $this->reload();
    }

    public function authorize(): bool
    {
        return $this->handler->isEnabledFor(auth()->user());
    }
}
