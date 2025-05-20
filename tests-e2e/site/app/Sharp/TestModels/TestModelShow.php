<?php

namespace App\Sharp\TestModels;

use App\Models\TestModel;
use App\Sharp\Commands\TestDownloadInstanceCommand;
use App\Sharp\Commands\TestFormInstanceCommand;
use App\Sharp\Commands\TestInfoInstanceCommand;
use App\Sharp\Commands\TestLinkInstanceCommand;
use App\Sharp\Commands\TestRefreshInstanceCommand;
use App\Sharp\Commands\TestReloadInstanceCommand;
use App\Sharp\Commands\TestViewInstanceCommand;
use App\Sharp\TestModelStateHandler;
use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Show\Layout\ShowLayout;
use Code16\Sharp\Show\Layout\ShowLayoutSection;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestModelShow extends SharpShow
{
    protected function buildShowFields(FieldsContainer $showFields): void
    {
        $showFields
            ->addField(
                SharpShowEntityListField::make('test-models')
                    ->setLabel('Test models')
                    ->showCount()
            );
    }

    protected function buildShowLayout(ShowLayout $showLayout): void
    {
        $showLayout
            ->addSection(function (ShowLayoutSection $section) {})
            ->addEntityListSection('test-models');
    }

    public function buildShowConfig(): void
    {
        $this->configurePageTitleAttribute('text')
            ->configureEntityState('state', TestModelStateHandler::class);
    }

    public function getInstanceCommands(): ?array
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

    public function find($id): array
    {
        return $this->transform(TestModel::findOrFail($id));
    }

    public function delete($id): void
    {
        TestModel::findOrFail($id)->delete();
    }
}
