<?php

namespace App\Sharp;

use App\Sharp\Commands\AccountUpdateName;
use App\Sharp\States\AccountStatusState;
use App\User;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpSingleShow;

class AccountSharpShow extends SharpSingleShow
{
    function buildShowFields(): void
    {
        $this
            ->addField(
                SharpShowTextField::make("name")
                    ->setLabel("Name:")
            )->addField(
                SharpShowTextField::make("email")
                    ->setLabel("Email:")
            )->addField(
                SharpShowTextField::make("groups")
                    ->setLabel("Groups:")
            );
    }

    function buildShowLayout(): void
    {
        $this
            ->addSection('Identity', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function(ShowLayoutColumn $column) {
                        $column
                            ->withSingleField("name")
                            ->withSingleField("email")
                            ->withSingleField("groups");
                    });
            });
    }

    function buildShowConfig(): void
    {
        $this
            ->addInstanceCommand("rename", AccountUpdateName::class)
            ->setEntityState("status", AccountStatusState::class);
    }

    function findSingle(): array
    {
        return $this->transform(User::findOrFail(auth()->id()));
    }
}