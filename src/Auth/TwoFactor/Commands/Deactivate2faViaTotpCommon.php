<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

use Closure;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

trait Deactivate2faViaTotpCommon
{
    protected ?Sharp2faHandler $handler = null;

    public function label(): ?string
    {
        return trans('sharp::auth.2fa.totp.commands.deactivate.command_label');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setInputTypePassword()
                    ->setLabel(trans('sharp::auth.2fa.totp.commands.activate.password_field_label'))
            );
    }

    protected function executeSingleOrEntity(array $data = []): array
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

        $this->get2faHandler()->setUser(auth()->user())->deactivate2faForUser();

        $this->notify('Your 2fa protection has been deactivated.')
            ->setLevelInfo();

        return $this->reload();
    }

    public function authorize(): bool
    {
        return $this->get2faHandler()->isEnabledFor(auth()->user());
    }

    private function get2faHandler(): Sharp2faHandler
    {
        if ($this->handler === null) {
            $this->handler = app(Sharp2faHandler::class);
        }

        return $this->handler;
    }
}
