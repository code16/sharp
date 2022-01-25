<?php

namespace App\Sharp;

use App\Pilot;
use App\Sharp\Commands\PilotDownloadPhoto;
use App\Sharp\Commands\PilotUpdateXPCommand;
use App\Sharp\Filters\PilotRoleFilter;
use App\Sharp\Filters\PilotSpaceshipFilter;
use App\Sharp\States\PilotEntityState;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class PilotSharpList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('name')
                    ->setSortable()
                    ->setLabel('Name'),
            )
            ->addField(
                EntityListField::make('role')
                    ->setLabel('Role'),
            )
            ->addField(
                EntityListField::make('xp')
                    ->setLabel('Xp'),
            );
    }

    public function getInstanceCommands(): ?array
    {
        return [
            PilotDownloadPhoto::class,
        ];
    }

    public function getEntityCommands(): ?array
    {
        return [
            PilotUpdateXPCommand::class,
        ];
    }

    public function getFilters(): array
    {
        return [
            PilotSpaceshipFilter::class,
            PilotRoleFilter::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this->configureSearchable()
            ->configureDefaultSort('name', 'asc')
            ->configureMultiformAttribute('role')
            ->configurePaginated()
            ->configureEntityState('state', PilotEntityState::class);
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        if ($role = $this->queryParams->filterFor('role')) {
            $fieldsLayout->addColumn('name', 6);
            if ($role === 'sr') {
                $fieldsLayout->addColumn('xp', 6);
            }
        } else {
            $fieldsLayout->addColumn('name', 4)
                ->addColumn('role', 4)
                ->addColumn('xp', 4);
        }
    }

    public function getListData(): array|Arrayable
    {
        $pilots = Pilot::select('pilots.*')->distinct();

        if ($ids = $this->queryParams->specificIds()) {
            $pilots->whereIn('id', $ids);
        } else {
            if ($spaceship = $this->queryParams->filterFor(PilotSpaceshipFilter::class)) {
                $pilots->leftJoin('pilot_spaceship', 'pilots.id', '=', 'pilot_spaceship.pilot_id')
                    ->leftJoin('spaceships', 'spaceships.id', '=', 'pilot_spaceship.spaceship_id')
                    ->where('spaceships.id', $spaceship);
            }

            if ($role = $this->queryParams->filterFor(PilotRoleFilter::class)) {
                $pilots->where('role', $role);
            }

            if ($this->queryParams->sortedBy()) {
                $pilots->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
            }

            if ($this->queryParams->hasSearch()) {
                foreach ($this->queryParams->searchWords() as $word) {
                    $pilots->where('pilots.name', 'like', $word);
                }
            }
        }

        return $this
            ->setCustomTransformer('role', function ($role, $pilot) {
                return $pilot->role == 'sr' ? 'senior' : 'junior';
            })
            ->setCustomTransformer('xp', function ($xp, $pilot) {
                return $pilot->role == 'sr' ? $xp.'y' : null;
            })
            ->transform($pilots->paginate(30));
    }
}
