<?php

namespace Code16\Sharp\Utils\Traits;

use Code16\Sharp\Form\Fields\Formatters\PreparesForValidation;
use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait HandleValidation
{
    final public function validate(array $params, array $rules, array $messages = []): void
    {
        $params = collect($params)
            ->map(function ($value, $key) {
                if (! $field = $this->findFieldByKey($key)) {
                    return $value;
                }

                if ($field->formatter() instanceof PreparesForValidation) {
                    return $field
                        ->formatter()
                        ->prepareForValidation($field, $key, $value);
                }

                return $value;
            })
            ->all();

        $validator = app(Validator::class)->make($params, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException(
                $validator,
                request()->wantsJson()
                    ? new JsonResponse($validator->errors()->getMessages(), 422)
                    : null,
            );
        }
    }
}
