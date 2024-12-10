<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    sharp()->config()
        ->displayBreadcrumb()
        ->addEntity('person', PersonEntity::class);
    login();
});

it('builds the breadcrumb for an entity list', function () {
    $this
        ->get(route('code16.sharp.list', ['person']))
        ->assertOk();

    expect(sharp()->context()->isEntityList())->toBeTrue()
        ->and(sharp()->context()->breadcrumb()->allSegments())->toHaveCount(1);
});

it('builds the breadcrumb for a show page', function () {
    $this
        ->get(
            route('code16.sharp.show.show', [
                'parentUri' => 's-list/person/',
                'person',
                1,
            ])
        )
        ->assertOk();

    expect(sharp()->context()->isShow())->toBeTrue()
        ->and(sharp()->context()->breadcrumb()->allSegments())->toHaveCount(2);
});

it('builds the breadcrumb for a single show page', function () {
    sharp()->config()->addEntity('single-person', SinglePersonEntity::class);

    $this
        ->get(route('code16.sharp.single-show', 'single-person'))
        ->assertOk();

    expect(sharp()->context()->isShow())->toBeTrue()
        ->and(sharp()->context()->breadcrumb()->allSegments())->toHaveCount(1);
});

it('builds the breadcrumb for a form', function () {
    $this
        ->get(
            route('code16.sharp.form.edit', [
                'parentUri' => 's-list/person/',
                'person',
                1,
            ])
        )
        ->assertOk();

    expect(sharp()->context()->isForm())->toBeTrue()
        ->and(sharp()->context()->breadcrumb()->allSegments())->toHaveCount(2);
});

it('builds the breadcrumb for a form through a show page', function () {
    $this
        ->get(
            route('code16.sharp.form.edit', [
                'parentUri' => 's-list/person/s-show/person/1',
                'person',
                1,
            ])
        )
        ->assertOk();

    expect(sharp()->context()->isForm())->toBeTrue()
        ->and(sharp()->context()->breadcrumb()->allSegments())->toHaveCount(3);
});

it('uses labels defined for entities in the config', function () {
    app(SharpEntityManager::class)
        ->entityFor('person')
        ->setLabel('Scientist');

    $this
        ->get(
            route('code16.sharp.show.show', [
                'parentUri' => 's-list/person/',
                'person',
                1,
            ])
        )
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('breadcrumb.items.0.label', 'List')
            ->where('breadcrumb.items.1.label', 'Scientist')
        );
});

it('uses custom labels on leaves if configured, based on unformatted data', function () {
    fakeShowFor('person', new class() extends PersonShow
    {
        public function buildShowConfig(): void
        {
            $this->configureBreadcrumbCustomLabelAttribute('name');
        }

        public function find($id): array
        {
            return $this->transform([
                'id' => 1,
                'name' => 'Marie Curie',
            ]);
        }
    });

    $this
        ->get(
            route('code16.sharp.show.show', [
                'parentUri' => 's-list/person/',
                'person',
                1,
            ])
        )
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('breadcrumb.items.0.label', 'List')
            // Data is not formatted for breadcrumb:
            ->where('breadcrumb.items.1.label', 'Marie Curie')
            // Data is formatted for fields:
            ->where('show.data.name', ['text' => 'Marie Curie'])
        );
});
