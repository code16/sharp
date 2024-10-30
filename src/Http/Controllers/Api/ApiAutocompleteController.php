<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ApiAutocompleteController extends ApiController
{
    public function index(string $entityKey, string $autocompleteFieldKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);
        
        sharp_check_ability(
            'view',
            $entityKey,
        );
        
        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        
        $field = $form->findFieldByKey($autocompleteFieldKey);
        
        if(!$field instanceof SharpFormAutocompleteRemoteField) {
            throw ValidationException::withMessages([
                'autocompleteFieldKey' => 'Unknown remote autocomplete field : '.$autocompleteFieldKey,
            ]);
        }
        
        request()->validate([
            'endpoint' => ['nullable', 'starts_with:'.str($field->remoteEndpoint())->before('{{')],
            'search' => ['required', 'string'],
        ]);
        
        if($callback = $field->getQueryResultsCallback()) {
            $data = $callback(request()->input('search'));
        } else {
            if(str($field->remoteEndpoint())->startsWith('/')) {
                $response = app()->handle(
                    tap(Request::create(
                        uri: url($field->remoteEndpoint()),
                        method: $field->remoteMethod(),
                        parameters: [
                            $field->remoteSearchAttribute() => request()->input('search'),
                        ],
                        cookies: request()->cookies->all(),
                    ), function(Request $request) use($field) {
                        $request->headers->set('Accept', 'application/json');
                    })
                );
                
                $data = json_decode($response->getContent());
            } else {
                $pendingRequest = Http::createPendingRequest()
                    ->acceptJson()
                    ->throw()
                    ->withQueryParameters([
                        $field->remoteSearchAttribute() => request()->input('search'),
                    ]);
                if($field->remoteMethod() === 'POST') {
                    $apiResponse = $pendingRequest->post(request()->input('endpoint'));
                } else {
                    $apiResponse = $pendingRequest->get(request()->input('endpoint'));
                }
                $data = $apiResponse->json();
            }
        }

        return response()->json([
            'data' => collect(Arr::get($data, $field->dataWrapper() ?: null))->map(fn ($item) => [
                ...$item,
                '_html' => $field->render($item),
            ]),
        ]);
    }
}
