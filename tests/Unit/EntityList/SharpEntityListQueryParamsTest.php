<?php

use Code16\Sharp\EntityList\EntityListQueryParams;
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
        ->toEqual('carpenter')
        ->and(buildParams(1, '', null, null, ['job' => 'carpenter,salesman'])->filterFor('job'))
        ->toEqual([
            'carpenter',
            'salesman',
        ])
        ->and(buildParams(1, '', null, null, ['range' => '20190201..20190210'])->filterFor('range'))
        ->toEqual([
            'start' => Carbon::createFromFormat('Y-m-d', '2019-02-01')->setTime(0, 0),
            'end' => Carbon::createFromFormat('Y-m-d', '2019-02-10')->setTime(23, 59, 59, 999999),
        ])
        ->and(buildParams()->filterFor('job'))->toBeNull();
});

function buildParams($p = 1, $s = '', $sb = null, $sd = null, $f = null): EntityListQueryParams
{
    return new class($p, $s, $sb, $sd, $f) extends EntityListQueryParams
    {
        public function __construct($p, $s, $sb, $sd, $f)
        {
            $this->page = $p;
            $this->search = $s;
            $this->sortedBy = $sb;
            $this->sortedDir = $sd;
            if ($f) {
                $this->filters = $f;
            }
        }
    };
}
