<?php

namespace App\Sharp;

use App\Pilot;
use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\Exceptions\Form\SharpApplicativeException;
use Code16\Sharp\Form\Eloquent\Transformers\EloquentFormTagsTransformer;
use Code16\Sharp\Form\Eloquent\Transformers\EloquentFormUploadTransformer;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutFieldset;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\Transformers\FormMarkdownTransformer;
use Code16\Sharp\Utils\Transformers\WithCustomTransformers;

class SpaceshipSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    use WithCustomTransformers;

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")

        )->addField(
            SharpFormTextField::make("capacity")
                ->setLabel("Capacity (x1000)")

        )->addField(
            SharpFormMarkdownField::make("description")
                ->setLabel("Description")
                ->setToolbar([
                    SharpFormMarkdownField::B, SharpFormMarkdownField::I,
                    SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::IMG,
                    SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::A,
                ])

        )->addField(
            SharpFormDateField::make("construction_date")
                ->setLabel("Construction date")
                ->setDisplayFormat("YYYY/MM/DD")
                ->setHasTime(false)

        )->addField(
            SharpFormAutocompleteField::make("type_id", "local")
                ->setLabel("Ship type")
                ->setSearchKeys(["label"])
                ->setListItemTemplatePath("/sharp/templates/spaceshipType_list.vue")
                ->setResultItemTemplatePath("/sharp/templates/spaceshipType_result.vue")
                ->setLocalValues(
                    SpaceshipType::orderBy("label")->get()->pluck("label", "id")->all()
                )

        )->addField(
            SharpFormUploadField::make("picture")
                ->setLabel("Picture")
                ->setFileFilterImages()
                ->setCropRatio("1:1")
                ->setStorageDisk("local")
                ->setStorageBasePath("data/Spaceship/{id}")

        )->addField(
            SharpFormTextField::make("picture:legend")
                ->setLabel("Legend")

        )->addField(
            SharpFormTagsField::make("pilots",
                    Pilot::orderBy("name")->get()->pluck("name", "id")->all()
                )
                ->setLabel("Pilots")
                ->setCreatable(true)
                ->setCreateAttribute("name")
                ->setMaxTagCount(4)

        )->addField(
            SharpFormListField::make("reviews")
                ->setLabel("Technical reviews")
                ->setAddable()
                ->setRemovable()
                ->setItemIdAttribute("id")
                ->addItemField(
                    SharpFormDateField::make("starts_at")
                        ->setLabel("Date")
                        ->setDisplayFormat("YYYY/MM/DD HH:mm")
                        ->setHasTime(true)
                )->addItemField(
                    SharpFormSelectField::make("status", [
                        "ok" => "Passed", "ko" => "Failed"
                    ])->setLabel("Status")
                    ->setDisplayAsList()
                )->addItemField(
                    SharpFormTextareaField::make("comment")
                        ->setLabel("Comment")
                        ->addConditionalDisplay("status", "ko")
                )
        )->addField(
            SharpFormListField::make("pictures")
                ->setLabel("Additional pictures")
                ->setAddable()->setAddText("Add a picture")
                ->setRemovable()
                ->setSortable()
                ->setItemIdAttribute("id")
                ->setOrderAttribute("order")
                ->addItemField(
                    SharpFormUploadField::make("file")
                        ->setFileFilterImages()
                        ->setCropRatio("16:9")
                        ->setStorageDisk("local")
                        ->setStorageBasePath("data/Pictures/{id}")
                )->addItemField(
                    SharpFormTextField::make("legend")
                        ->setPlaceholder("Legend")
                )
        );
    }

    function buildFormLayout()
    {
        $this->addTab("tab 1", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function(FormLayoutColumn $column) {
                $column->withSingleField("name")
                    ->withSingleField("type_id")
                    ->withSingleField("pilots")
                    ->withSingleField("reviews", function(FormLayoutColumn $listItem) {
                        $listItem->withSingleField("starts_at")
                            ->withFields("status|5", "comment|7");
                    });
            })->addColumn(6, function(FormLayoutColumn $column) {
                $column->withSingleField("picture")
                    ->withSingleField("picture:legend")
                    ->withSingleField("pictures", function(FormLayoutColumn $listItem) {
                        $listItem->withSingleField("file")
                            ->withSingleField("legend");
                    });
            });

        })->addTab("tab 2", function(FormLayoutTab $tab) {
            $tab->addColumn(5, function(FormLayoutColumn $column) {
                $column->withFieldset("Technical details", function(FormLayoutFieldset $fieldset) {
                    return $fieldset->withFields("capacity|4,6", "construction_date|8,6");
                });
            })->addColumn(7, function(FormLayoutColumn $column) {
                $column->withSingleField("description");
            });
        });
    }

    function create(): array
    {
        return $this->transform(new Spaceship(["name" => "new"]));
    }

    function find($id): array
    {
        return $this->setCustomTransformer("capacity", function($spaceship) {
                return $spaceship->capacity / 1000;
            })
            ->setCustomTransformer("pilots", new EloquentFormTagsTransformer("name"))
            ->setCustomTransformer("description", new FormMarkdownTransformer())
            ->setCustomTransformer("picture", new EloquentFormUploadTransformer())
            ->setCustomTransformer("pictures", new EloquentFormUploadTransformer())
            ->transform(
                Spaceship::with("reviews", "pilots", "picture", "pictures")->findOrFail($id)
            );
    }

    function update($id, array $data)
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

        if($data["name"] == "error") {
            throw new SharpApplicativeException("Name can't be «error»");
        }

        $this->setCustomValuator("capacity", function ($spaceship, $value) {
                return $value * 1000;
            })
//            ->ignore("pilots")
            ->save($instance, $data);
    }

    function delete($id)
    {
        Spaceship::findOrFail($id)->delete();
    }
}