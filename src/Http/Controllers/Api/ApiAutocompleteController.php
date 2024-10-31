<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Utils\Transformers\ArrayConverter;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class ApiAutocompleteController extends ApiController
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
            $data = collect($callback(request()->input('search')))->map(fn ($record) => ArrayConverter::modelToArray($record));
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
                
                $data = Arr::get(json_decode($response->getContent(), true), $field->dataWrapper() ?: null);
//            }
        }

        return response()->json([
            'data' => collect($data)->map(fn ($item) => $field->itemWithRenderedTemplates($item)),
        ]);
    }
}
