<?php

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Filters\DateRangeFilter;
use Code16\Sharp\Filters\FilterContainer\FilterContainer;
use Code16\Sharp\Filters\SelectMultipleFilter;
use Illuminate\Support\Carbon;

it('handles hasSearch', function () {
    expect(buildParams(1, 'test')->hasSearch())->toBeTrue()
        ->and(buildParams(1, '')->hasSearch())->toBeFalse();
});

it('explodes a search in words', function () {
    expect(buildParams(1, 'the little cat is dead')->searchWords())
        ->toBe(['%the%', '%little%', '%cat%', '%is%', '%dead%']);
});

it('allows to search without like', function () {
    expect(buildParams(1, 'the little cat is dead')->searchWords(false))
        ->toBe(['the', 'little', 'cat', 'is', 'dead']);
});

it('allows to use a star in search', function () {
    expect(buildParams(1, 'cat*')->searchWords())
        ->toBe(['cat%'])
        ->and(buildParams(1, '*cat')->searchWords())
        ->toBe(['%cat']);
});

it('finds filter values', function () {
    expect(buildParams(1, '', null, null, ['job' => 'carpenter'])->filterFor('job'))
        ->toEqual('carpenter');

    expect(buildParams(1, '', null, null, ['job' => 'carpenter,salesman'])->filterFor('job'))
        ->toEqual([
            'carpenter',
            'salesman',
        ]);

    expect(buildParams(1, '', null, null, ['range' => '20190201..20190210'])->filterFor('range'))
        ->toBeInstanceOf(\Code16\Sharp\Filters\DateRange\DateRangeFilterValue::class)
        ->getStart()->toEqual(Carbon::parse('20190201'))
        ->getEnd()->toEqual(Carbon::parse('20190210'));

    expect(buildParams()->filterFor('job'))->toBeNull();
});

function buildParams($p = 1, $s = '', $sb = null, $sd = null, $filters = null): EntityListQueryParams
{
    return new class($p, $s, $sb, $sd, $filters) extends EntityListQueryParams
    {
        public function __construct($p, $s, $sb, $sd, $f)
        {
            $filterContainer = new FilterContainer(
                collect($f)
                    ->map(function ($value, $key) {
                        if (str($value)->contains('..')) {
                            return new class($key) extends DateRangeFilter
                            {
                                public function __construct(string $key)
                                {
                                    $this->customKey = $key;
                                }
                            };
                        }

                        if (str($value)->contains(',')) {
                            return new class($key) extends SelectMultipleFilter
                            {
                                public function __construct(string $key)
                                {
                                    $this->customKey = $key;
                                }
                                public function values(): array
                                {
                                    return [];
                                }
                            };
                        }

                        return HiddenFilter::make($key);
                    })
                    ->values()
                    ->toArray()
            );

            parent::__construct(
                filterContainer: $filterContainer,
                filterValues: [
                    ...$filterContainer->getFilterValuesFromQueryParams(
                        collect($f)->mapWithKeys(fn ($v, $k) => ["filter_$k" => $v])->toArray()
                    ),
                ],
                sortedBy: $sb,
                sortedDir: $sd,
                page: $p,
                search: $s
            );
        }
    };
}
