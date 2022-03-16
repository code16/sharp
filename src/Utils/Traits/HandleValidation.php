<?php

namespace Code16\Sharp\Utils\Traits;

use Illuminate\Contracts\Validation\Factory as Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

trait HandleValidation
{
    final public function validate(array $params, array $rules, array $messages = []): void
    {
        $validator = app(Validator::class)->make($params, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException(
                $validator, new JsonResponse($validator->errors()->getMessages(), 422),
            );
        }
    }
}
