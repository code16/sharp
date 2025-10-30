<?php

namespace App\Sharp\Profile;

use App\Sharp\Profile\Commands\Activate2faCommand;
use App\Sharp\Profile\Commands\Deactivate2faCommand;
use App\Sharp\Profile\Commands\UpdateProfilePasswordCommand;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;

class ProfileSingleShow extends SharpSingleShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(
                SharpShowTextField::make('email')
                    ->setLabel('Email address'),
            )
            ->addField(
                SharpShowPictureField::make('avatar'),
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection('', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, fn (ShowLayoutColumn $column) => $column->withField('email'))
                    ->addColumn(6, fn (ShowLayoutColumn $column) => $column->withField('avatar'));
            });
    }

    public function buildShowConfig(): void
    {
        $this->configurePageTitleAttribute('name')
            ->configureBreadcrumbCustomLabelAttribute('name');
    }

    public function getInstanceCommands(): ?array
    {
        return [
            UpdateProfilePasswordCommand::class,
            ...sharp()->config()->get('auth.2fa.handler') === 'totp'
                ? [Activate2faCommand::class, Deactivate2faCommand::class]
                : [],
        ];
    }

    public function findSingle(): array
    {
        return $this
            ->setCustomTransformer('avatar', new SharpUploadModelThumbnailUrlTransformer(140))
            ->transform(auth()->user());
    }
}
