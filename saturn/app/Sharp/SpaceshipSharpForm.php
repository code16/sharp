<?php

namespace App\Sharp;

use App\Spaceship;
use App\SpaceshipType;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentUpdater;
use Code16\Sharp\Form\Eloquent\WithSharpFormEloquentTransformer;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Form\SharpFormException;

class SpaceshipSharpForm extends SharpForm
{
    use WithSharpFormEloquentUpdater;

    use WithSharpFormEloquentTransformer;

    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("name")
                ->setLabel("Name")

        )->addField(
            SharpFormTextField::make("capacity")
                ->setLabel("Capacity (x1000)")

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
                    SpaceshipType::orderBy("label")->get()->map(function($item) {
                        return [
                            "id" => $item->id,
                            "label" => $item->label
                        ];
                    })->all()
                )

        )->addField(
            SharpFormUploadField::make("picture")
                ->setLabel("Picture")
                ->setFileFilterImages()
                ->setCropRatio("1:1")
                ->setStorageDisk("local")
                ->setStorageBasePath("data/Spaceship/{id}")

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
        );
    }

    function buildFormLayout()
    {
        $this->addTab("tab 1", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function($column) {
                $column->withSingleField("name")
                    ->withSingleField("type_id");
            })->addColumn(6, function($column) {
                $column->withSingleField("picture")
                    ->withSingleField("reviews", function($item) {
                        $item->withSingleField("starts_at")
                            ->withFields("status|5", "comment|7");
                    });
            });

        })->addTab("tab 2", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function($column) {
                $column->withFieldset("Technical details", function($fieldset) {
                    return $fieldset->withFields("capacity|4,6", "construction_date|8,6");
                });
            });
        });

    }

//    function create()
//    {
//        return $this->transform(new Spaceship);
//    }

    function find($id): array
    {
        return $this->setCustomTransformer("capacity", function($spaceship) {
            return $spaceship->capacity / 1000;

        })->transform(
            Spaceship::findOrFail($id)
        );
    }

    function update($id, array $data): bool
    {
        $instance = $id ? Spaceship::findOrFail($id) : new Spaceship;

        if($data["name"] == "error") {
            throw new SharpFormException("Name can't be «error»");
        }

        return $this->setCustomValuator("capacity", function($spaceship, $value) {
            return $value * 1000;

        })->save($instance, $data);
    }

    function delete($id): bool
    {
        return true;
    }
}