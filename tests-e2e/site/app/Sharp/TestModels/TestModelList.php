<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use App\Sharp\Commands\TestDownloadEntityCommand;
use App\Sharp\Commands\TestDownloadInstanceCommand;
use App\Sharp\Commands\TestFormEntityCommand;
use App\Sharp\Commands\TestFormInstanceCommand;
use App\Sharp\Commands\TestInfoEntityCommand;
use App\Sharp\Commands\TestInfoInstanceCommand;
use App\Sharp\Commands\TestLinkEntityCommand;
use App\Sharp\Commands\TestLinkInstanceCommand;
use App\Sharp\Commands\TestRefreshEntityCommand;
use App\Sharp\Commands\TestRefreshInstanceCommand;
use App\Sharp\Commands\TestReloadEntityCommand;
use App\Sharp\Commands\TestReloadInstanceCommand;
use App\Sharp\Commands\TestSelectionCommand;
use App\Sharp\Commands\TestViewEntityCommand;
use App\Sharp\Commands\TestViewInstanceCommand;
use App\Sharp\Filters\TestAutocompleteRemoteFilter;
use App\Sharp\Filters\TestCheckFilter;
use App\Sharp\Filters\TestDateRangeFilter;
use App\Sharp\Filters\TestDateRangeRequiredFilter;
use App\Sharp\Filters\TestSelectFilter;
use App\Sharp\Filters\TestSelectMultipleFilter;
use App\Sharp\Filters\TestSelectRequiredFilter;
use App\Sharp\TestModelStateHandler;
use Code16\Sharp\EntityList\Eloquent\SimpleEloquentReorderHandler;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Filters\DateRange\DateRangeFilterValue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class TestModelList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('id')
                    ->setLabel('Id')
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('text')
                    ->setLabel('Text')
                    ->setSortable()
            )
            ->addField(
                EntityListField::make('textarea')
                    ->setLabel('Textarea')
            );
    }

    public function buildListConfig(): void
    {
        if (session()->get('default_sort')) {
            $this->configureDefaultSort(session()->get('default_sort'));
        }

        if (session()->get('quick_creation_form')) {
            $this->configureQuickCreationForm();
        }

        if (session()->get('entity_list_multiform')) {
            $this->configureMultiformAttribute('form');
        }

        $this
            ->configureEntityState('state', TestModelStateHandler::class)
            ->configureSearchable()
            ->configureReorderable(new SimpleEloquentReorderHandler(TestModel::class));
    }

    protected function getInstanceCommands(): ?array
    {
        return [
            TestFormInstanceCommand::class,
            TestDownloadInstanceCommand::class,
            TestInfoInstanceCommand::class,
            TestLinkInstanceCommand::class,
            TestViewInstanceCommand::class,
            TestReloadInstanceCommand::class,
            TestRefreshInstanceCommand::class,
        ];
    }

    protected function getEntityCommands(): ?array
    {
        return [
            TestFormEntityCommand::class,
            TestSelectionCommand::class,
            TestDownloadEntityCommand::class,
            TestInfoEntityCommand::class,
            TestLinkEntityCommand::class,
            TestViewEntityCommand::class,
            TestReloadEntityCommand::class,
            TestRefreshEntityCommand::class,
        ];
    }

    protected function getFilters(): array
    {
        return [
            TestAutocompleteRemoteFilter::class,
            TestCheckFilter::class,
            TestDateRangeFilter::class,
            TestDateRangeRequiredFilter::class,
            TestSelectFilter::class,
            TestSelectMultipleFilter::class,
            TestSelectRequiredFilter::class,
        ];
    }

    public function getListData(): array|Arrayable
    {
        return $this
            ->transform(
                TestModel::query()
                    ->when(
                        $this->queryParams->specificIds(),
                        fn (Builder $builder, array $ids) => $builder->whereIn('id', $ids),
                    )
                    ->when($this->queryParams->filterFor(TestAutocompleteRemoteFilter::class), function (Builder $query, $value) {
                        $query->where('select_dropdown', $value);
                    })
                    ->when($this->queryParams->filterFor(TestCheckFilter::class), function (Builder $query, $check) {
                        $query->where('check', $check);
                    })
                    ->when($this->queryParams->filterFor(TestDateRangeFilter::class), function (Builder $query, DateRangeFilterValue $dateRange) {
                        $query->whereBetween('date', [
                            $dateRange->getStart(),
                            $dateRange->getEnd(),
                        ]);
                    })
                    ->when($this->queryParams->filterFor(TestSelectFilter::class), function (Builder $query, $value) {
                        $query->where('select_dropdown', $value);
                    })
                    ->when($this->queryParams->filterFor(TestSelectMultipleFilter::class), function (Builder $query, $value) {
                        $query->whereIn('select_dropdown', $value);
                    })
                    ->when($this->queryParams->hasSearch(), function (Builder $query) {
                        collect($this->queryParams->searchWords())->each(function ($word) use ($query) {
                            $query->where(function (Builder $query) use ($word) {
                                $query->orWhere('text', 'like', $word);
                            });
                        });
                    })
                    ->when($this->queryParams->sortedBy(),
                        fn (Builder $query) => $query->orderBy($this->queryParams->sortedBy(), $this->queryParams->sortedDir()),
                        fn (Builder $query) => $query->orderBy('order')
                    )
                    ->paginate(5)
            );
    }
}
