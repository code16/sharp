<?php

namespace App\Sharp\Profile;

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
                SharpShowTextField::make('name')
                    ->setLabel('Name'),
            )
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
            ->addSection('Informations', function (ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField('name')
                            ->withSingleField('email');
                    })
                    ->addColumn(6, function (ShowLayoutColumn $column) {
                        $column
                            ->withSingleField('avatar');
                    });
            });
    }

    public function findSingle(): array
    {
        return $this
            ->setCustomTransformer('avatar', new SharpUploadModelThumbnailUrlTransformer(140))
            ->transform(auth()->user());
    }
}
