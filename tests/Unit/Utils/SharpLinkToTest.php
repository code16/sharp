<?php

use Code16\Sharp\Exceptions\SharpInvalidBreadcrumbItemException;
use Code16\Sharp\Utils\Filters\SelectFilter;
use Code16\Sharp\Utils\Links\BreadcrumbBuilder;
use Code16\Sharp\Utils\Links\LinkToDashboard;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Links\LinkToForm;
use Code16\Sharp\Utils\Links\LinkToShowPage;
use Code16\Sharp\Utils\Links\LinkToSingleForm;
use Code16\Sharp\Utils\Links\LinkToSingleShowPage;

it('allows to generate a link to an entity list', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity" title="">test</a>',
        LinkToEntityList::make('my-entity')
            ->renderAsText('test'),
    );
});

it('allows to generate a link to a form', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity/s-form/my-entity/23" title="">test</a>',
        LinkToForm::make('my-entity', 23)
            ->renderAsText('test'),
    );
});

it('allows to generate a link to a form through a show page', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity/s-show/my-entity/23/s-form/my-entity/23" title="">test</a>',
        LinkToForm::make('my-entity', 23)
            ->throughShowPage()
            ->renderAsText('test'),
    );
});

it('allows to generate a link to a show page', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity/s-show/my-entity/23" title="">test</a>',
        LinkToShowPage::make('my-entity', 23)
            ->renderAsText('test'),
    );
});

it('allows to generate a link to a single show page', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-show/my-entity" title="">test</a>',
        LinkToSingleShowPage::make('my-entity')
            ->renderAsText('test'),
    );
});

it('allows to generate a link to a dashboard page', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-dashboard/my-entity" title="">test</a>',
        LinkToDashboard::make('my-entity')
            ->renderAsText('test'),
    );
});

it('allows to generate an url to a show page with a specific breadcrumb', function () {
    $this->assertEquals(
        'http://localhost/sharp/s-list/base-entity/s-show/base-entity/3/s-show/my-entity/4',
        LinkToShowPage::make('my-entity', 4)
            ->withBreadcrumb(function (BreadcrumbBuilder $builder) {
                return $builder
                    ->appendEntityList('base-entity')
                    ->appendShowPage('base-entity', 3);
            })
            ->renderAsUrl(),
    );
});

it('allows to generate an url to a form with a specific breadcrumb', function () {
    $this->assertEquals(
        'http://localhost/sharp/s-list/base-entity/s-show/base-entity/3/s-show/my-entity/4/s-form/my-entity/4',
        LinkToForm::make('my-entity', 4)
            ->withBreadcrumb(function (BreadcrumbBuilder $builder) {
                return $builder
                    ->appendEntityList('base-entity')
                    ->appendShowPage('base-entity', 3);
            })
            ->throughShowPage()
            ->renderAsUrl(),
    );
});

it('allows to generate an url to a show page with a specific breadcrumb starting with a single show page', function () {
    $this->assertEquals(
        'http://localhost/sharp/s-show/base-entity/s-show/my-entity/4',
        LinkToShowPage::make('my-entity', 4)
            ->withBreadcrumb(function (BreadcrumbBuilder $builder) {
                return $builder->appendSingleShowPage('base-entity');
            })
            ->renderAsUrl(),
    );
});

it('allows to not generate an url with a specific breadcrumb starting with a show', function () {
    $this->expectException(SharpInvalidBreadcrumbItemException::class);

    LinkToShowPage::make('my-entity', 4)
        ->withBreadcrumb(function (BreadcrumbBuilder $builder) {
            return $builder->appendShowPage('base-entity', 3);
        })
        ->renderAsUrl();
});

it('allows to not push a entity list anywhere else than in the first spot', function () {
    $this->expectException(SharpInvalidBreadcrumbItemException::class);

    LinkToShowPage::make('my-entity', 4)
        ->withBreadcrumb(function (BreadcrumbBuilder $builder) {
            return $builder->appendShowPage('base-entity', 3)
                ->appendEntityList('base-entity');
        })
        ->renderAsUrl();
});

it('allows to generate a link to an entity single form', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-show/my-entity/s-form/my-entity" title="">test</a>',
        LinkToSingleForm::make('my-entity')
            ->renderAsText('test'),
    );
});

it('allows to generate a link to an entity list with a search', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity?search=my-search" title="">test</a>',
        LinkToEntityList::make('my-entity')
            ->setSearch('my-search')
            ->renderAsText('test'),
    );
});

it('allows to generate a link to an entity list with a filter', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity?filter_country=France&filter_city=Paris" title="">test</a>',
        LinkToEntityList::make('my-entity')
            ->addFilter('country', 'France')
            ->addFilter('city', 'Paris')
            ->renderAsText('test'),
    );
});

it('allows to generate a link to an entity list with a filter classname', function () {
    $filter = new class extends SelectFilter
    {
        public function buildFilterConfig(): void
        {
            $this->configureKey('my-key');
        }

        public function values(): array
        {
            return [
                1 => 'one',
                2 => 'two',
            ];
        }
    };

    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity?filter_my-key=1" title="">test</a>',
        LinkToEntityList::make('my-entity')
            ->addFilter($filter::class, 1)
            ->renderAsText('test'),
    );
});

it('allows to generate a link to an entity list with a sort', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity?sort=name&dir=desc" title="">test</a>',
        LinkToEntityList::make('my-entity')
            ->setSort('name', 'desc')
            ->renderAsText('test'),
    );
});

it('allows to generate a link with a tooltip', function () {
    $this->assertEquals(
        '<a href="http://localhost/sharp/s-list/my-entity" title="tooltip">test</a>',
        LinkToEntityList::make('my-entity')
            ->setTooltip('tooltip')
            ->renderAsText('test'),
    );
});

it('allows to generate an url', function () {
    $this->assertEquals(
        'http://localhost/sharp/s-list/my-entity',
        LinkToEntityList::make('my-entity')
            ->renderAsUrl(),
    );
});
