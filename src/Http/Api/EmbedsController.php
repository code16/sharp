<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Layout\FormLayoutColumn;
use Code16\Sharp\Utils\Fields\FieldsContainer;
use Illuminate\Routing\Controller;

class EmbedsController extends Controller
{
    public function show(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        return response()->json([
            'embeds' => collect(request()->get('embeds'))->map(fn ($attributes) => [
                ...$attributes,
                'status' => 'active', // todo (test data)
            ]),
        ]);
    }

    public function showForm(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        return [
            'config' => null,
            'fields' => collect(
                (new FieldsContainer())
                ->addField(SharpFormTextField::make('content'))
                ->getFields(),
            )->map->toArray()->keyBy('key')->all(),
            'layout' => (new FormLayoutColumn(12))
                ->withSingleField('content')
                ->fieldsToArray()['fields'],
            'data' => request()->all(),
        ];
    }

    public function update(string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        return [
            'attributes' => [
                'content' => request()->get('content'),
            ],
        ];
    }
}
