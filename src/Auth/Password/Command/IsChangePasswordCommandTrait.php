<?php

namespace Code16\Sharp\Auth\Password\Command;

use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

trait IsChangePasswordCommandTrait
{
    private bool $confirmPassword = false;
    private bool $validateCurrentPassword = true;
    private ?Password $passwordRule = null;

    public function label(): ?string
    {
        return trans('sharp::auth.password_change.command.label');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->when(
                $this->validateCurrentPassword,
                fn (FieldsContainer $formFields) => $formFields->addField(
                    SharpFormTextField::make('password')
                        ->setLabel(trans('sharp::auth.password_change.command.fields.current_password'))
                        ->setInputTypePassword()
                )
            )
            ->addField(
                SharpFormTextField::make('new_password')
                    ->setLabel(trans('sharp::auth.password_change.command.fields.new_password'))
                    ->setInputTypePassword()
            )
            ->when(
                $this->confirmPassword,
                fn (FieldsContainer $formFields) => $formFields->addField(
                    SharpFormTextField::make('new_password_confirmation')
                        ->setLabel(trans('sharp::auth.password_change.command.fields.new_password_confirm'))
                        ->setInputTypePassword()
                )
            );
    }

    public function rules(): array
    {
        $rules = RateLimiter::attempt(
            'sharp-password-change-'.auth()->id(),
            3,
            fn () => [
                ...$this->validateCurrentPassword
                    ? ['password' => ['required', 'current_password']]
                    : [],
                'new_password' => [
                    'required',
                    'string',
                    $this->passwordRule ?? Password::min(8),
                    ...$this->confirmPassword ? ['confirmed'] : [],
                ],
            ],
        );

        if (! $rules) {
            throw new SharpApplicativeException(
                trans(
                    'sharp::auth.password_change.command.rate_limit_exceeded', [
                        'seconds' => RateLimiter::availableIn('sharp-password-change-'.auth()->id()),
                    ]
                )
            );
        }

        return $rules;
    }

    protected function configureConfirmPassword(?bool $confirmPassword = true): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    protected function configureValidateCurrentPassword(?bool $validateCurrentPassword = true): self
    {
        $this->validateCurrentPassword = $validateCurrentPassword;

        return $this;
    }

    protected function configurePasswordRule(Password $passwordRule): self
    {
        $this->passwordRule = $passwordRule;

        return $this;
    }
}
