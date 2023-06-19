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
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

trait Activate2faViaTotpWizardCommandTrait
{
    protected ?Sharp2faHandler $handler = null;

    public function label(): ?string
    {
        return trans('sharp::auth.2fa.totp.commands.activate.command_label');
    }

    protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setInputTypePassword()
                    ->setLabel(trans('sharp::auth.2fa.totp.commands.activate.password_field_label'))
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

                    if (! auth()->validate($credentials)) {
                        $fail(trans('sharp::auth.invalid_credentials'));
                    }
                },
            ],
        ]);

        $this->get2faHandler()->setUser(auth()->user())->initialize();

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
            ->writeString($this->get2faHandler()->setUser(auth()->user())->getQRCodeUrl());

        return [
            'qr' => [
                'svg' => trim(substr($svg, strpos($svg, "\n") + 1)),
            ],
        ];
    }

    protected function buildFormFieldsForStepConfirm(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormHtmlField::make('qr')
                    ->setLabel(trans('sharp::auth.2fa.totp.commands.activate.qrcode_field_label'))
                    ->setInlineTemplate('<div style="text-align: center; margin: 1em 0;" v-html="svg"></div>')
            )
            ->addField(
                SharpFormTextField::make('code')
                    ->setMaxLength(6)
                    ->setLabel(trans('sharp::auth.2fa.totp.commands.activate.code_field_label'))
            );
    }

    protected function executeStepConfirm(array $data): array
    {
        $this->validate($data, [
            'code' => [
                'required',
                'numeric',
            ],
        ]);

        if ($this->get2faHandler()->setUser(auth()->user())->checkCode($data['code'])) {
            $this->get2faHandler()->activate2faForUser();

            return $this->toStep('show_recovery_codes');
        }

        throw new SharpApplicativeException(trans('sharp::auth.2fa.invalid'));
    }

    protected function initialDataForStepShowRecoveryCodes(): array
    {
        return [
            'recovery_codes' => collect($this->get2faHandler()->setUser(auth()->user())->getRecoveryCodes())
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
                    ->setLabel(trans('sharp::auth.2fa.totp.commands.activate.recovery_codes_field_label'))
                    ->setHelpMessage(trans('sharp::auth.2fa.totp.commands.activate.recovery_codes_field_help'))
            );
    }

    protected function executeStepShowRecoveryCodes(array $data): array
    {
        return $this->reload();
    }

    public function authorize(): bool
    {
        return $this->isStep('show_recovery_codes')
            || ! $this->get2faHandler()->isEnabledFor(auth()->user());
    }

    private function get2faHandler(): Sharp2faHandler
    {
        if ($this->handler === null) {
            $this->handler = app(Sharp2faHandler::class);
        }

        return $this->handler;
    }
}
