<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Http\Controllers\Api\Embeds\HandleEmbed;
use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ApiFormAutocompleteController extends ApiController
{
    use HandleEmbed;
    
    public function index(string $entityKey, string $autocompleteFieldKey, ?string $embedKey = null)
    {
        if ($embedKey) {
            $formOrEmbed = $this->getEmbedFromKey($embedKey);
        } else {
            $entity = $this->entityManager->entityFor($entityKey);
            $formOrEmbed = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        }
        
        $field = $formOrEmbed->findFieldByKey($autocompleteFieldKey);

        if ($field === null) {
            throw new SharpInvalidConfigException('Remote autocomplete field '.$autocompleteFieldKey.' was not found in form.');
        } elseif (! $field instanceof SharpFormAutocompleteRemoteField) {
            throw new SharpInvalidConfigException('The field '.$autocompleteFieldKey.' is not a remote autocomplete field.');
        }

        if ($callback = $field->getRemoteCallback()) {
            $formData = request()->input('formData')
                ? collect(request()->input('formData'))
                    ->filter(fn ($value, $key) => in_array($key, $formOrEmbed->getDataKeys()))
                    ->map(function ($value, $key) use ($formOrEmbed) {
                        if (! $field =  $formOrEmbed->findFieldByKey($key)) {
                            return $value;
                        }

                        return $field
                            ->formatter()
                            ->setDataLocalizations($formOrEmbed->getDataLocalizations())
                            ->fromFront($field, $key, $value);
                    })
                    ->toArray()
                : null;

            $data = collect($callback(request()->input('search'), $formData))
                ->map(fn ($record) => ArrayConverter::modelToArray($record));
        } else {
            // Local endpoint case
            $requestEndpoint = $this->normalizeEndpoint(request()->input('endpoint'));
            $fieldEndpoint = $this->normalizeEndpoint($field->remoteEndpoint());

            // Check that requestEndpoint is valid
            $this->checkEndpoint($requestEndpoint, $fieldEndpoint);

            $response = app()->handle(
                tap(Request::create(
                    uri: $requestEndpoint,
                    method: $field->remoteMethod(),
                    parameters: [
                        $field->remoteSearchAttribute() => request()->input('search'),
                    ],
                    cookies: request()->cookies->all(),
                ), fn (Request $request) => $request->headers->set('Accept', 'application/json'))
            );

            if ($response->getStatusCode() >= 400) {
                abort($response);
            }

            $data = Arr::get(json_decode($response->getContent(), true), $field->dataWrapper() ?: null);
        }

        return response()->json([
            'data' => collect($data)->map(fn ($item) => $field->itemWithRenderedTemplates($item)),
        ]);
    }

    private function normalizeEndpoint(string $input): string
    {
        return str($input)->startsWith('http') ? $input : url($input);
    }

    private function checkEndpoint(string $requestEndpoint, string $fieldEndpoint): void
    {
        collect(Route::getRoutes()->getRoutes())
            ->map(fn ($route) => url($route->uri))
            ->filter(fn ($routeUrl) => $routeUrl == $requestEndpoint)
            ->count() > 0 ?: throw new SharpInvalidConfigException('The endpoint is not a valid internal route.');

        preg_match(
            '#'.str()
                ->of(preg_quote($fieldEndpoint))
                ->replaceMatches('#\\\\{\\\\{(.*)\\\\}\\\\}#', '(.*)').'#im',
            $requestEndpoint
        ) ?: throw new SharpInvalidConfigException('The endpoint is not a valid internal route.');
    }
}
