<?php

namespace App\Sharp;

use App\Sharp\Commands\TravelSendEmail;
use App\Travel;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

class TravelSharpList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
        $fieldsContainer
            ->addField(
                EntityListField::make('destination')
                    ->setSortable()
                    ->setLabel('Destination')
            )
            ->addField(
                EntityListField::make('departure_date')
                    ->setSortable()
                    ->setLabel('Departure date')
            )
            ->addField(
                EntityListField::make('spaceship')
                    ->setLabel('Spaceship')
            );
    }

    public function getInstanceCommands(): ?array
    {
        return [
            'send-email' => TravelSendEmail::class,
        ];
    }

    public function buildListConfig(): void
    {
        $this//->setSearchable()
            ->configureDefaultSort('departure_date', 'desc')
            ->configurePaginated();
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
        $fieldsLayout->addColumn('destination', 4)
            ->addColumn('departure_date', 4)
            ->addColumn('spaceship', 4);
    }

    public function getListData(): array|Arrayable
    {
        $travels = Travel::query();

        if ($this->queryParams->sortedBy()) {
            $travels->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir());
        }

        if ($this->queryParams->hasSearch()) {
            foreach ($this->queryParams->searchWords() as $word) {
                $travels->where('destination', 'like', $word);
            }
        }

        return $this
            ->setCustomTransformer('spaceship', function ($value, $travel) {
                if (!$travel->spaceship) {
                    return '';
                }

                return '<i class="fas fa-space-shuttle"></i> '.$travel->spaceship->name;
            })
            ->transform($travels->with(['spaceship'])->paginate(30));
    }
}
