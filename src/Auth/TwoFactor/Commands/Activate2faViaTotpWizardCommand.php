<?php

namespace Code16\Sharp\Auth\TwoFactor\Commands;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Closure;
use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class Activate2faViaTotpWizardCommand extends EntityWizardCommand
{
    public function __construct(protected Sharp2faHandler $handler)
    {
    }

    public function label(): ?string
    {
        return trans('sharp::auth.2fa.totp_commands.activate.command_label');
    }

    protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setInputTypePassword()
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.password_field_label'))
            );
    }

    protected function executeFirstStep(array $data): array
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

                    if (!auth()->validate($credentials)) {
                        $fail(trans('sharp::auth.invalid_credentials'));
                    }
                },
            ],
        ]);
        
        $this->handler->setUser(auth()->user())->initialize();

        return $this->toStep('confirm');
    }
    
    protected function initialDataForStepConfirm(): array
    {
        $svg = (
            new Writer(
                new ImageRenderer(
                    new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                    new SvgImageBackEnd
                )
            ))
            ->writeString($this->handler->setUser(auth()->user())->getQRCodeUrl());

        return [
            'qr' => [
                'svg' => trim(substr($svg, strpos($svg, "\n") + 1))
            ],
        ];
    }

    protected function buildFormFieldsForStepConfirm(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('qr')
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.qrcode_field_label'))
                    ->setInlineTemplate('<div style="text-align: center; margin: 1em 0;" v-html="svg"></div>')
            )
            ->addField(
                SharpFormTextField::make('code')
                    ->setMaxLength(6)
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.code_field_label'))
            );
    }

    protected function executeStepConfirm(array $data): array
    {
        $this->validate($data, [
            'code' => [
                'required', 
                'numeric'
            ],
        ]);
        
        if($this->handler->setUser(auth()->user())->checkCode($data['code'])) {
            $this->handler->confirmUser();

            return $this->toStep('show_recovery_codes');
        }

        throw new SharpApplicativeException(trans('sharp::auth.2fa.invalid'));
    }

    protected function initialDataForStepShowRecoveryCodes(): array
    {
        return [
            'recovery_codes' => collect($this->handler->setUser(auth()->user())->getRecoveryCodes())
                ->implode("\n"),
        ];
    }

    protected function buildFormFieldsForStepShowRecoveryCodes(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextareaField::make('recovery_codes')
                    ->setRowCount(8)
                    ->setReadOnly()
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.recovery_codes_field_label'))
                    ->setHelpMessage(trans('sharp::auth.2fa.totp_commands.activate.recovery_codes_field_help'))
            );
    }

    protected function executeStepShowRecoveryCodes(array $data): array
    {
        return $this->reload();
    }
    
    public function authorize(): bool
    {
        return $this->isStep('show_recovery_codes') 
            || !$this->handler->isEnabledFor(auth()->user());
    }
}