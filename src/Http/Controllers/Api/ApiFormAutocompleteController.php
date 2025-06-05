<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesEntityCommand;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesInstanceCommand;
use Code16\Sharp\Http\Controllers\Api\Embeds\HandlesEmbed;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ApiFormAutocompleteController extends ApiController
{
    use HandlesEmbed;
    use HandlesEntityCommand;
    use HandlesInstanceCommand;

    public function index(EntityKey $entityKey, string $autocompleteFieldKey)
    {
        $fieldContainer = $this->getFieldContainer($entityKey);
        $field = $fieldContainer->findFieldByKey($autocompleteFieldKey);

        if ($field === null) {
            throw new SharpInvalidConfigException('Remote autocomplete field '.$autocompleteFieldKey.' was not found in form.');
        } elseif (! $field instanceof SharpFormAutocompleteRemoteField) {
            throw new SharpInvalidConfigException('The field '.$autocompleteFieldKey.' is not a remote autocomplete field.');
        }

        if ($callback = $field->getRemoteCallback()) {
            $listFieldKey = str_contains($autocompleteFieldKey, '.')
                ? str($autocompleteFieldKey)->before('.')
                : null;

            $formData = request()->input('formData')
                ? collect(request()->input('formData'))
                    ->mapWithKeys(function ($value, $key) use ($fieldContainer, $listFieldKey) {
                        if (! $field = $fieldContainer->findFieldByKey($listFieldKey ? "$listFieldKey.$key" : $key)) {
                            return [];
                        }

                        return [
                            $key => $field
                                ->formatter()
                                ->setDataLocalizations($fieldContainer->getDataLocalizations())
                                ->fromFront($field, $key, $value),
                        ];
                    })
                    ->toArray()
                : null;

            return response()->json([
                'data' => collect($callback(request()->input('search'), $formData))
                    ->map(fn ($item) => $field->itemWithRenderedTemplates($item)),
            ]);
        }

        // Local endpoint case
        $requestEndpoint = $this->normalizeEndpoint(request()->input('endpoint'));
        $fieldEndpoint = $this->normalizeEndpoint($field->remoteEndpoint());

        // Check that requestEndpoint is valid
        $this->checkEndpoint($requestEndpoint, $fieldEndpoint);

        $apiResponse = app()->handle(
            tap(Request::create(
                uri: $requestEndpoint,
                method: $field->remoteMethod(),
                parameters: [
                    $field->remoteSearchAttribute() => request()->input('search'),
                ],
                cookies: Request::createFromGlobals()->cookies->all(), // raw encrypted cookies
            ), fn (Request $request) => $request->headers->set('Accept', 'application/json'))
        );

        if ($apiResponse->getStatusCode() >= 400) {
            abort($apiResponse);
        }

        $data = Arr::get(json_decode($apiResponse->getContent(), true), $field->dataWrapper() ?: null);

        return response()->json([
            'data' => collect($data)->map(fn ($item) => $field->itemWithRenderedTemplates($item)),
        ]);
    }

    private function getFieldContainer(EntityKey $entityKey): SharpFormEditorEmbed|Command|SharpForm
    {
        if (request()->input('embed_key')) {
            return $this->getEmbedFromKey(request()->input('embed_key'));
        }

        $entity = $this->entityManager->entityFor($entityKey);

        if ($commandKey = request()->input('entity_list_command_key')) {
            if (request()->input('instance_id')) {
                return $this->getInstanceCommandHandler(
                    $entity->getListOrFail(),
                    $commandKey,
                    request()->input('instance_id')
                );
            }

            return $this->getEntityCommandHandler(
                $entity->getListOrFail(),
                $commandKey
            );
        }

        if ($commandKey = request()->input('show_command_key')) {
            return $this->getInstanceCommandHandler(
                $entity->getShowOrFail(),
                $commandKey,
                request()->input('instance_id')
            );
        }

        return $entity->getFormOrFail($entityKey->multiformKey());
    }

    private function getFormattedData() {}

    private function normalizeEndpoint(string $input): string
    {
        return str($input)->startsWith('http') ? $input : url($input);
    }

    private function checkEndpoint(string $requestEndpoint, string $fieldEndpoint): void
    {
        // Validates that the endpoint defined in the field is the same as the one called
        preg_match(
            '#'
            .str()
                ->of(preg_quote($fieldEndpoint))
                ->replaceMatches('#\\\\{\\\\{(.*)\\\\}\\\\}#', '(.*)')
            .'#im',
            $requestEndpoint
        ) ?: throw new SharpInvalidConfigException('The endpoint is not the one defined in the autocomplete field.');

        // Check that the remote route exists in the app
        collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => url($route->uri))
            ->filter(fn (string $routeUrl) => preg_match(
                '#'.str()->of($routeUrl)->replaceMatches('#\\{(.*)\\}#', '(.*)').'#im',
                str($requestEndpoint)->before('?')
            ))
            ->count() > 0 ?: throw new SharpInvalidConfigException('The endpoint is not a valid internal route.');
    }
}
