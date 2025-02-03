<?php

namespace Database\Seeders;

use App\Data\SeedParametersData;
use App\Models\TestModel;
use App\Models\TestTag;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run(SeedParametersData $parameters)
    {
        if ($parameters->tags) {
            TestTag::insert(
                TestTag::factory()
                    ->forEachSequence(
                        ['label' => 'Tag 1'],
                        ['label' => 'Tag 2'],
                        ['label' => 'Tag 3'],
                        ['label' => 'Tag 4'],
                        ['label' => 'Tag 5'],
                        ['label' => 'Tag 6'],
                        ['label' => 'Tag 7'],
                        ['label' => 'Tag 8'],
                        ['label' => 'Tag 9'],
                        ['label' => 'Tag 10'],
                    )
                    ->raw()
            );
        }

        if ($parameters->entityList) {
            $models = TestModel::factory()
                ->forEachSequence(
                    ['text' => 'check', 'check' => true],
                    ['text' => 'select option 1', 'select_dropdown' => 1],
                    ['text' => 'select option 2', 'select_dropdown' => 2],
                    ['text' => 'search result'],
                )
                ->create();
            TestModel::insert(
                collect(
                    TestModel::factory()
                        ->sequence(fn (Sequence $sequence) => [
                            'text' => 'Test Model '.($sequence->index + 1),
                            'textarea' => 'Textarea '.$sequence->count() - $sequence->index,
                        ])
                        ->count(20)
                        ->raw()
                )->skip(count($models))->all(),
            );
        }

        if ($parameters->show) {
            TestModel::factory()
                ->create([
                    'text' => 'Example',
                ]);
        }
    }
}
