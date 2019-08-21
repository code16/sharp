<?php

namespace App\Sharp;

use App\User;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;

class AccountSharpShow extends SharpSingleShow
{
    function buildShowFields()
    {
        $this
            ->addField(
                SharpShowTextField::make("name")
                    ->setLabel("Name:")
            )->addField(
                SharpShowTextField::make("email")
                    ->setLabel("Email:")
            )->addField(
                SharpShowTextField::make("group")
                    ->setLabel("Groups:")
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
                            ->withSingleField("email")
                            ->withSingleField("groups");
                    });
            });
    }

    function findSingle(): array
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }
}