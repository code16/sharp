<?php

namespace App\Sharp\Dashboard\Commands;

use App\Sharp\Utils\Filters\PeriodRequiredFilter;
use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Code16\Sharp\Utils\PageAlerts\PageAlert;

class ExportStatsAsCsvCommand extends DashboardCommand
{
    public function label(): ?string
    {
        return 'Export stats as a CSV file...';
    }

    protected function buildPageAlert(PageAlert $pageAlert): void
    {
        $pageAlert
            ->setLevelInfo()
            ->setMessage(function ($instanceId, array $data) {
                return sprintf(
                    'For the period %s - %s',
                    $data['period']['start'],
                    $data['period']['end'],
                );
            });
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormSelectField::make('stats', ['authors' => 'Posts by author', 'category' => 'Posts by category', 'visits' => 'Visits'])
                    ->setMultiple()
                    ->setLabel('Stats to export'),
            );
    }

    public function execute(array $data = []): array
    {
        $this->validate($data, [
            'stats' => ['required', 'array'],
        ]);

        return $this->streamDownload('some stats', 'stats.csv');
    }

    protected function initialData(): array
    {
        return [
            'period' => [
                'start' => $this->queryParams->filterFor(PeriodRequiredFilter::class)['start']->isoFormat('L'),
                'end' => $this->queryParams->filterFor(PeriodRequiredFilter::class)['end']->isoFormat('L'),
            ],
        ];
    }
}
