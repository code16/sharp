<?php

namespace Code16\Sharp\Auth\Password\Command;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Validation\Rules\Password;

trait IsChangePasswordCommandTrait
{
    private bool $confirmPassword = false;
    private ?Password $passwordRule = null;

    public function label(): ?string
    {
        return trans('sharp::auth.password_change.command.label');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setLabel(trans('sharp::auth.password_change.command.fields.current_password'))
                    ->setInputTypePassword()
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
        return [
            'password' => [
                'required',
                'current_password',
            ],
            'new_password' => [
                'required',
                'string',
                $this->passwordRule ?? Password::min(8),
                ...$this->confirmPassword ? ['confirmed'] : [],
            ],
        ];
    }

    protected function configureConfirmPassword(?bool $confirmPassword = true): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    protected function configurePasswordRule(Password $passwordRule): self
    {
        $this->passwordRule = $passwordRule;

        return $this;
    }
}
