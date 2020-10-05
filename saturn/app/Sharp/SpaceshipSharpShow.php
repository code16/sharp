<?php

namespace App\Sharp;

use App\Sharp\Commands\SpaceshipExternalLink;
use App\Sharp\Commands\SpaceshipPreview;
use App\Sharp\Commands\SpaceshipSendMessage;
use App\Sharp\CustomShowFields\SharpShowTitleField;
use App\Sharp\States\SpaceshipEntityState;
use App\Spaceship;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowFileField;
use Code16\Sharp\Show\Fields\SharpShowListField;
use Code16\Sharp\Show\Fields\SharpShowPictureField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
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
                SharpShowTitleField::make("name")
                    ->setTitleLevel(2)
            )->addField(
                SharpShowTextField::make("type:label")
                    ->setLabel("Type")
            )->addField(
                SharpShowTextField::make("serial_number")
                    ->setLabel("S/N")
            )->addField(
                SharpShowTextField::make("brand")
                    ->setLabel("Brand / model")
            )->addField(
                SharpShowFileField::make("manual")
                    ->setLabel("Manual")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data/Spaceship/{id}/Manual")
            )->addField(
                SharpShowPictureField::make("picture")
            )->addField(
                SharpShowTextField::make("description")
                    ->collapseToWordCount(50)
            )->addField(
                SharpShowListField::make("pictures")
                    ->setLabel("additional pictures")
                    ->addItemField(
                        SharpShowFileField::make("file")
                            ->setStorageDisk("local")
                            ->setStorageBasePath("data/Spaceship/{id}/Pictures")
                    )
                    ->addItemField(SharpShowTextField::make("legend")->setLabel("Legend"))
            )->addField(
                SharpShowEntityListField::make("pilots", "spaceship_pilot")
                    ->setLabel("Pilots")
                    ->hideFilterWithValue("spaceship", function($instanceId) {
                        return $instanceId;
                    })
                    ->showEntityState(false)
//                    ->hideEntityCommand("updateXP")
//                    ->hideInstanceCommand("download")
                    ->showReorderButton(true)
                    ->showCreateButton()
            );
    }

    /**
     * @throws \Code16\Sharp\Exceptions\SharpException
     */
    function buildShowConfig()
    {
        $this
            ->addInstanceCommand("message", SpaceshipSendMessage::class)
            ->addInstanceCommand("preview", SpaceshipPreview::class)
            ->addInstanceCommandSeparator()
            ->addInstanceCommand("external", SpaceshipExternalLink::class)
            ->setEntityState("state", SpaceshipEntityState::class);
    }

    function buildShowLayout()
    {
        $this
            ->addSection('Identity', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function(ShowLayoutColumn $column) {
                        $column
                            ->withSingleField("name")
                            ->withSingleField("type:label")
                            ->withSingleField("serial_number")
                            ->withSingleField("brand")
                            ->withSingleField("manual");
                    })
                    ->addColumn(5, function(ShowLayoutColumn $column) {
                        $column->withSingleField("picture");
                    });
            })
            ->addSection('Description', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(6, function(ShowLayoutColumn $column) {
                        $column->withSingleField("description");
                    })
                    ->addColumn(6, function(ShowLayoutColumn $column) {
                        $column->withSingleField("pictures", function(ShowLayoutColumn $listItem) {
                            $listItem->withSingleField("file")
                                ->withSingleField("legend");
                        });
                    });
            })
            ->addEntityListSection("pilots");
    }

    function find($id): array
    {
        return $this
            ->setCustomTransformer("brand", function($value, $spaceship) {
                return sprintf(
                    "%s / %s",
                    $spaceship->brand ?: '<em>no brand</em>',
                    $spaceship->model ?: '<em>no model</em>'
                );
            })
            ->setCustomTransformer("name", function($value, $spaceship) {
                return $spaceship->name;
            })
            ->setCustomTransformer("manual", new SharpUploadModelFormAttributeTransformer(false))
            ->setCustomTransformer("picture", new SharpUploadModelThumbnailUrlTransformer(140))
            ->setCustomTransformer("pictures", new SharpUploadModelFormAttributeTransformer(true, 200, 200))
            ->setCustomTransformer("pictures[legend]", function($value, $instance) {
                return $instance->legend["en"] ?? "";
            })
            ->setCustomTransformer("description", (new MarkdownAttributeTransformer())->handleImages(200))
            ->transform(Spaceship::with("manual", "pictures")->findOrFail($id));
    }
}
