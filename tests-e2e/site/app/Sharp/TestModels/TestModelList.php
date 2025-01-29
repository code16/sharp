<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use App\Sharp\Filters\EntityList\TestCheckFilter;
use App\Sharp\Filters\EntityList\TestDateRangeFilter;
use App\Sharp\Filters\EntityList\TestDateRangeRequiredFilter;
use App\Sharp\Filters\EntityList\TestSelectFilter;
use App\Sharp\Filters\EntityList\TestSelectMultipleFilter;
use App\Sharp\Filters\EntityList\TestSelectRequiredFilter;
use App\Sharp\TestCommand;
use Code16\Sharp\EntityList\Fields\EntityListField;
use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Utils\Filters\DateRangeFilterValue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

class TestModelList extends SharpEntityList
{
    protected function buildList(EntityListFieldsContainer $fields): void
    {
        $fields
            ->addField(
                EntityListField::make('text')
                    ->setLabel('Text'),
            );
    }

    public function buildListConfig(): void {}

    protected function getInstanceCommands(): ?array
    {
        return [];
    }

    protected function getEntityCommands(): ?array
    {
        return [
            TestCommand::class,
        ];
    }

    protected function getFilters(): array
    {
        return [
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
                    ->when($this->queryParams->filterFor(TestCheckFilter::class), function(Builder $query, $check) {
                        $query->where('check', $check);
                    })
                    ->when($this->queryParams->filterFor(TestDateRangeFilter::class), function(Builder $query, DateRangeFilterValue $dateRange) {
                        $query->whereBetween('date', [
                            $dateRange->getStart(),
                            $dateRange->getEnd(),
                        ]);
                    })
                    ->when($this->queryParams->filterFor(TestSelectFilter::class), function(Builder $query, $value) {
                        $query->where('select_dropdown', $value);
                    })
                    ->when($this->queryParams->filterFor(TestSelectMultipleFilter::class), function(Builder $query, $value) {
                        $query->whereIn('select_dropdown', $value);
                    })
                    ->paginate(5)
            );
    }
}
