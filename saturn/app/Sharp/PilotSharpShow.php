<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\Commands\PilotDownloadPhoto;
use App\Sharp\States\PilotEntityState;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Show\Layout\ShowLayoutColumn;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;

class PilotSharpShow extends SharpShow
{
    function buildShowFields(): void
    {
        $this
            ->addField(
                SharpShowTextField::make("name")
                    ->setLabel("Name")
            )->addField(
                SharpShowTextField::make("role")
                    ->setLabel("Role")
            )->addField(
                SharpShowTextField::make("xp")
                    ->setLabel("Xp")
            )->addField(
                SharpShowEntityListField::make("spaceships", "spaceship")
                    ->setLabel("Spaceships")
                    ->hideFilterWithValue("pilots", function($instanceId) {
                        return $instanceId;
                    })
                    ->hideEntityCommand(["synchronize", "reload"])
                    ->showCreateButton(false)
            );
    }

    function buildShowConfig(): void
    {
        $this
//            ->setMultiformAttribute("role")
            ->setEntityState("state", PilotEntityState::class)
            ->addInstanceCommand("download", PilotDownloadPhoto::class);
    }

    function buildShowLayout(): void
    {
        $this
            ->addSection('Identity', function(ShowLayoutSection $section) {
                $section
                    ->addColumn(7, function(ShowLayoutColumn $column) {
                        $column
                            ->withSingleField("name")
                            ->withFields("role|6", "xp|6");
                    });
            })
            ->addEntityListSection("spaceships");
    }

    function find($id): array
    {
        return $this
            ->setCustomTransformer("role", function($role, $pilot) {
                return $pilot->role == "sr" ? "senior" : "junior";
            })
            ->setCustomTransformer("xp", function($xp, $pilot) {
                return $pilot->role == "sr" ? $xp . "y" : null;
            })
            ->transform(Pilot::with("spaceships")->findOrFail($id));
    }
}
