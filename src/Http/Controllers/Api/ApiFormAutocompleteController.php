<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class ApiFormAutocompleteController extends ApiController
{
    public function index(string $entityKey, string $autocompleteFieldKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);
        
//        sharp_check_ability(
//            'view',
//            $entityKey,
//        );
        
        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        
        $field = $form->findFieldByKey($autocompleteFieldKey);
        
        if (!$field instanceof SharpFormAutocompleteRemoteField) {
            throw ValidationException::withMessages([
                'autocompleteFieldKey' => 'Unknown remote autocomplete field : '.$autocompleteFieldKey,
            ]);
        }
        
        request()->validate([
            'endpoint' => ['nullable', 'starts_with:'.str($field->remoteEndpoint())->before('{{')],
            'search' => ['nullable', 'string'],
        ]);
        
        if ($callback = $field->getQueryResultsCallback()) {
            $formData = request()->input('formData')
                ? collect(request()->input('formData'))
                    ->filter(fn ($value, $key) => in_array($key, $form->getDataKeys()))
                    ->map(function ($value, $key) use ($form) {
                        if (! $field = $form->findFieldByKey($key)) {
                            return $value;
                        }
                        return $field
                            ->formatter()
                            ->setDataLocalizations($form->getDataLocalizations())
                            ->fromFront($field, $key, $value);
                    })
                    ->toArray()
                : null;
            $data = collect($callback(request()->input('search'), $formData))
                ->map(fn ($record) => ArrayConverter::modelToArray($record));
        } else {
//            if($field->isExternalEndpoint()) {
//                $pendingRequest = Http::createPendingRequest()
//                    ->acceptJson()
//                    ->throw()
//                    ->withQueryParameters([
//                        $field->remoteSearchAttribute() => request()->input('search'),
//                    ]);
//                if($field->remoteMethod() === 'POST') {
//                    $apiResponse = $pendingRequest->post(request()->input('endpoint'));
//                } else {
//                    $apiResponse = $pendingRequest->get(request()->input('endpoint'));
//                }
//                $data = $apiResponse->json();
//                app(Router::class)->getRoutes()->getRoutes()[0]->matches(Request::create(request()->input('endpoint')));

//            } else {
                $response = app()->handle(
                    tap(Request::create(
                        uri: url($field->remoteEndpoint()), // TODO allow full URLs (route(...)) = check if route exists
                        method: $field->remoteMethod(),
                        parameters: [
                            $field->remoteSearchAttribute() => request()->input('search'),
                        ],
                        cookies: request()->cookies->all(),
                    ), function(Request $request) use($field) {
                        $request->headers->set('Accept', 'application/json');
                    })
                );
                
                $data = Arr::get(json_decode($response->getContent(), true), $field->dataWrapper() ?: null);
                if($response->getStatusCode() >= 400) {
                    abort($response);
                }
//            }
        }

        return response()->json([
            'data' => collect($data)->map(fn ($item) => $field->itemWithRenderedTemplates($item)),
        ]);
    }
}
