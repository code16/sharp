<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormDateField;
use Code16\Sharp\Form\Fields\SharpFormGeolocationField;
use Code16\Sharp\Form\Fields\SharpFormHtmlField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Form\Fields\SharpFormNumberField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Fields\SharpFormWysiwygField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Form\Layout\FormLayoutTab;
use Code16\Sharp\Form\SharpForm;

class TestForm extends SharpForm
{
    function buildFormFields()
    {
        $this->addField(
            SharpFormTextField::make("text")
                ->setLocalized()
                ->setLabel("Text")

        )->addField(
            SharpFormAutocompleteField::make("autocomplete_local", "local")
                ->setLabel("Autocomplete local")
                ->setLocalSearchKeys(["label"])
                ->setListItemInlineTemplate("{{label}}")
                ->setResultItemInlineTemplate("{{label}} ({{id}})")
                ->setLocalValues($this->options())

        )->addField(
            SharpFormAutocompleteField::make("autocomplete_remote", "remote")
                ->setLabel("Autocomplete remote")
                ->setRemoteSearchAttribute("query")
                ->setListItemInlineTemplate("{{name}}")
                ->setResultItemInlineTemplate("{{name}} ({{num}})")
                ->setRemoteEndpoint(url("/passengers"))

        )->addField(
            SharpFormAutocompleteListField::make("autocomplete_list")
                ->setLabel("Autocomplete_list")
                ->setAddable()
                ->setRemovable()
                ->setItemField(
                    SharpFormAutocompleteField::make("item", "remote")
                        ->setLabel("Passenger")
                        ->setPlaceholder("test")
                        ->setListItemInlineTemplate("{{ name }}")
                        ->setResultItemInlineTemplate("{{name}} ({{num}})")
                        ->setRemoteEndpoint(url('/passengers'))
                )
        )->addField(
            SharpFormCheckField::make("check", "Check")

        )->addField(
            SharpFormDateField::make("date")
                ->setLabel("Date")
                ->setDisplayFormat("YYYY-MM-DD HH:mm")
                ->setHasTime(true)

        )->addField(
            SharpFormGeolocationField::make("geolocation")
                ->setLabel("Geolocation")
                ->setApiKey(env("GMAPS_KEY"))
//                ->setDisplayUnitDecimalDegrees()
                ->setDisplayUnitDegreesMinutesSeconds()
                ->setGeocoding()
                ->setInitialPosition(48.5838961, 7.742182599999978)

        )->addField(
            SharpFormHtmlField::make("html")
                ->setLabel("Html")
                ->setInlineTemplate("Your name is <strong>{{name}}</strong>")

        )->addField(
            SharpFormListField::make("list")
                ->setLabel("List")
                ->setAddable()
                ->setSortable()
                ->setRemovable()
                ->setItemIdAttribute("id")
                ->addItemField(
                    SharpFormDateField::make("date")
                        ->setLabel("Date")
                        ->setDisplayFormat("YYYY/MM/DD")
                        ->setHasTime(false)
                )->addItemField(
                    SharpFormTextField::make("text")
                        ->setLabel("Text")
                )

        )->addField(
            SharpFormMarkdownField::make("markdown")
                ->setLabel("Markdown")
                ->setToolbar([
                    SharpFormMarkdownField::B, SharpFormMarkdownField::I, SharpFormMarkdownField::A,
                    SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::H1,
                    SharpFormMarkdownField::H2,
                    SharpFormMarkdownField::H3,
                    SharpFormMarkdownField::HR,
                    SharpFormMarkdownField::SEPARATOR,
                    SharpFormMarkdownField::OL,
                    SharpFormMarkdownField::UL,
                    SharpFormMarkdownField::QUOTE,
                    SharpFormMarkdownField::CODE,
                    SharpFormMarkdownField::IMG,
                ])
                ->setCropRatio("1:1")
                ->setHeight(350)
                ->setStorageDisk("local")
                ->setStorageBasePath("data")

        )->addField(
            SharpFormNumberField::make("number")
                ->setLabel("Number")
            ->setMin(1)
            ->setMax(100)

        )->addField(
            SharpFormSelectField::make("select_dropdown", $this->options())
                ->setLabel("Select dropdown")
                ->setDisplayAsDropdown()

        )->addField(
            SharpFormSelectField::make("select_list", $this->options())
                ->setLabel("Select list")
                ->setDisplayAsList()

        )->addField(
            SharpFormSelectField::make("select_list_multiple", $this->options())
                ->setLabel("Select list multiple")
                ->setMultiple()
                ->setDisplayAsList()

        )->addField(
            SharpFormTagsField::make("tags", $this->options())
                ->setLabel("Tags")
                ->setCreatable(true)
                ->setCreateAttribute("label")
                ->setMaxTagCount(4)

        )->addField(
            SharpFormTextareaField::make("textarea")
                ->setLocalized()
                ->setLabel("Textarea")
                ->setRowCount(4)

        )->addField(
            SharpFormUploadField::make("upload")
                ->setLabel("Upload")
                ->setFileFilterImages()
                ->setCropRatio("1:1")
                ->setStorageDisk("local")
                ->setStorageBasePath("data")

        )->addField(
            SharpFormWysiwygField::make("wysiwyg")
                ->setLocalized()
                ->setLabel("Wysiwyg")
                ->setToolbar([
                    SharpFormWysiwygField::B, SharpFormWysiwygField::I, SharpFormWysiwygField::A,
                    SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::H1,
                    SharpFormWysiwygField::OL,
                    SharpFormWysiwygField::UL,
                    SharpFormWysiwygField::QUOTE,
                    SharpFormWysiwygField::CODE,
                    SharpFormWysiwygField::SEPARATOR,
                    SharpFormWysiwygField::INCREASE_NESTING,
                    SharpFormWysiwygField::DECREASE_NESTING,
                    SharpFormWysiwygField::UNDO,
                    SharpFormWysiwygField::REDO,
                ])
                ->setHeight(150)
        );
    }

    function buildFormLayout()
    {
        $this->addTab("Simple", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("text")
                    ->withSingleField("date")
                    ->withSingleField("check");
            })->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("number")
                    ->withSingleField("html");
            });

        })->addTab("Textarea", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("markdown")
                    ->withSingleField("textarea");
            })->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("wysiwyg");
            });

        })->addTab("Select", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("autocomplete_local")
                    ->withSingleField("autocomplete_remote")
                    ->withSingleField("select_dropdown");

            })->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("select_list")
                    ->withSingleField("select_list_multiple")
                    ->withSingleField("tags");
            });

        })->addTab("List", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("autocomplete_list", function (FormLayoutColumn $listItem) {
                    $listItem->withSingleField("item");
                });
            })->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("list", function (FormLayoutColumn $listItem) {
                    $listItem->withFields("date|5", "text|7");
                });
            });

        })->addTab("Special", function(FormLayoutTab $tab) {
            $tab->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("geolocation");
            })->addColumn(6, function (FormLayoutColumn $column) {
                $column->withSingleField("upload");
            });
        });
    }

    function find($id): array
    {
        $faker = \Faker\Factory::create();

        return $this->transform([
            "text" => [
                "fr" => $faker->words(3, true),
                "en" => $faker->words(3, true),
            ],
            "autocomplete_local" => 1,
            "autocomplete_remote" => null,
            "autocomplete_list" => null,
            "check" => true,
            "date" => $faker->date("Y-m-d H:i"),
            "html" => [
                "name" => $faker->name
            ],
            "markdown" => "Some **text** with *style*",
            "number" => $faker->numberBetween(1, 100),
            "textarea" => [
                "fr" => $faker->paragraph(3),
                "en" => $faker->paragraph(3),
            ],
            "wysiwyg" => [
                "fr" => 'des <strong>trucs en html</strong>',
                "en" => 'some <strong>html stuff</strong>',
            ]
        ]);
    }

    function update($id, array $data)
    {
    }

    function delete($id)
    {
    }

    /**
     * @return array
     */
    protected function options()
    {
        return [
            "1" => "Option one",
            "2" => "Option two",
            "3" => "Option three",
        ];
    }
}