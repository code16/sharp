<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\EntityList\Eloquent\Transformers\SharpUploadModelThumbnailUrlTransformer;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class SpaceshipSharpShow extends SharpShow
{
    function buildShowFields()
    {
        $this
            ->addField(
                SharpShowTextField::make("name")
                    ->setLabel("Ship name:")
            )->addField(
                SharpShowTextField::make("type:label")
                    ->setLabel("Type:")
            )->addField(
                SharpShowTextField::make("serial_number")
                    ->setLabel("S/N:")
            )->addField(
                SharpShowTextField::make("brand")
                    ->setLabel("Brand / model:")
            )->addField(
                SharpShowPictureField::make("picture")
            );
    }

    function buildShowLayout()
    {
        $this
            ->addSection('Identity', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function(FormLayoutColumn $column) {
                        $column
                            ->withSingleField("name")
                            ->withSingleField("type:label")
                            ->withSingleField("serial_number")
                            ->withSingleField("brand");
                    })
                    ->addColumn(5, function(FormLayoutColumn $column) {
                        $column
                            ->withSingleField("picture");
                    });
            });
    }

    function find($id): array
    {
        return $this
            ->setCustomTransformer("brand", function($value, $spaceship) {
                return sprintf("%s / %s", $spaceship->brand ?: '<em>no brand</em>', $spaceship->model ?: '<em>no model</em>');
            })
            ->setCustomTransformer("name", function($value, $spaceship) {
                return $spaceship->name;
            })
            ->setCustomTransformer("picture", new SharpUploadModelThumbnailUrlTransformer(250))
            ->transform(Spaceship::findOrFail($id));
    }
}