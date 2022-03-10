<?php

namespace App\Sharp\Dashboard\Commands;

use App\Sharp\Utils\Filters\PeriodRequiredFilter;
use Code16\Sharp\Dashboard\Commands\DashboardCommand;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ExportStatsAsCsvCommand extends DashboardCommand
{

    public function label(): ?string
    {
        return "Export stats as a CSV file...";
    }
    
    public function buildCommandConfig(): void
    {
        $this->configurePageAlert('For the period {start} - {end}', static::$pageAlertLevelInfo, 'period');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormSelectField::make("stats", ["1" => "Posts by author", "2" => "Posts by category", "3" => "Visits"])
                    ->setMultiple()
                    ->setLabel("Stats to export")
            );
    }
    
    public function execute(array $data = []): array
    {
        $this->validate($data, [
            "stats" => ["required", "array"]
        ]);
        
        return $this->streamDownload("some stats", "stats.csv");
    }

    protected function initialData(): array
    {
        return [
            'period' => [
                'start' => $this->queryParams->filterFor(PeriodRequiredFilter::class)["start"],
                'end' => $this->queryParams->filterFor(PeriodRequiredFilter::class)["end"],
            ]
        ];
    }
}