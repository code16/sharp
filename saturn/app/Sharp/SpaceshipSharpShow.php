<?php

namespace App\Sharp;

use App\Spaceship;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Transformers\Attributes\Eloquent\SharpUploadModelThumbnailUrlTransformer;
use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;

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
            )->addField(
                SharpShowTextField::make("description")
                    ->collapseToWordCount(150)
            )->addField(
                SharpShowEntityListField::make("pilots", "pilot")
                    ->showFilters(["role"])
                    ->setFilterValue("spaceship", function($instanceId) {
                        return $instanceId;
                    })
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
                        $column->withSingleField("picture");
                    });
            })
            ->addSection('Description', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(9, function(FormLayoutColumn $column) {
                        $column->withSingleField("description");
                    });
            })
            ->addEntityListSection('Pilots', "pilots");
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
            ->setCustomTransformer("description", new MarkdownAttributeTransformer())
            ->transform(Spaceship::findOrFail($id));
    }
}