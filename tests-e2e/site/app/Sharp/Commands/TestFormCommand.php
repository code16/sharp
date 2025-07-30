<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Form\Fields\SharpFormCheckField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

trait TestFormCommand
{
    public function buildTestFormCommandFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(SharpFormCheckField::make('required', 'Require text field'))
            ->addField(SharpFormTextField::make('text')->setLabel('Text'))
            ->addField(SharpFormSelectField::make('test_action', [
                'download' => 'Download',
                'info' => 'Info',
                'link' => 'Link',
                'reload' => 'Reload',
                'refresh' => 'Refresh',
                'view' => 'View',
            ]));
    }

    public function executeTestFormCommand(mixed $instanceId, array $data = []): array
    {
        if ($data['required']) {
            $this->validate($data, [
                'text' => 'required',
            ]);
        }

        return match ($data['test_action']) {
            'download' => $this->download('file.pdf', 'file.pdf', 'fixtures'),
            'info' => $this->info('Info message : '.$data['text']),
            'link' => $this->link(route('test-page')),
            'reload' => tap($this->reload(), function () {
                TestModel::query()->update(['text' => 'Reloaded']);
            }),
            'refresh' => $this->refresh(
                tap(TestModel::pluck('id')->all(), function () {
                    TestModel::query()->update(['text' => 'Refreshed']);
                })
            ),
            'view' => $this->view('command-view', ['title' => 'Command view'])
        };
    }
}
