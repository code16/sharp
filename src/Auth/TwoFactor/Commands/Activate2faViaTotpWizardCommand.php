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
use Code16\Sharp\EntityList\Commands\Wizards\InstanceWizardCommand;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class Activate2faViaTotpWizardCommand extends InstanceWizardCommand
{
    public function __construct(protected Sharp2faHandler $handler)
    {
    }

    public function label(): ?string
    {
        return trans('sharp::auth.2fa.totp_commands.activate.label');
    }

    protected function buildFormFieldsForFirstStep(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormCheckField::make('activate', trans('sharp::auth.2fa.totp_commands.activate.check.label'))
            );
    }

    protected function executeFirstStep(mixed $instanceId, array $data): array
    {
        if($data['activate'] ?? false) {
            $this->handler->setUser(auth()->user())->initialize();
            
            return $this->toStep('password');
        }
            
        return $this->reload();
    }

    protected function buildFormFieldsForStepPassword(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('password')
                    ->setInputTypePassword()
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.password.label'))
            );
    }

    protected function executeStepPassword(mixed $instanceId, array $data): array
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

        return $this->toStep('confirm');
    }
    
    protected function initialDataForStepConfirm(mixed $instanceId): array
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
                    ->setInlineTemplate('<div v-html="svg"></div>')
            )
            ->addField(
                SharpFormTextField::make('code')
                    ->setLabel(trans('sharp::auth.2fa.totp_commands.activate.code.label'))
            );
    }

    protected function executeStepConfirm(mixed $instanceId, array $data): array
    {
        $this->validate($data, [
            'code' => [
                'required', 
                'numeric'
            ],
        ]);
        
        if($this->handler->setUser(auth()->user())->checkCode($data['code'])) {
            $this->handler->confirmUser();

            return $this->reload();
        }

        throw new SharpApplicativeException(trans('sharp::auth.2fa.invalid'));
    }
    
    public function authorize(): bool
    {
        return ! $this->handler->isEnabledFor(auth()->user());
    }
}