<?php

namespace Code16\Sharp\Http\Requests;

use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImpersonateRequest extends FormRequest
{
    private SharpImpersonationHandler $handler;

    public function authorize(): bool
    {
        return $this->getHandler()->enabled();
    }

    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::in(array_keys($this->getHandler()->getUsers()))
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => trans('sharp::auth.impersonate_error'),
        ];
    }

    private function getHandler(): SharpImpersonationHandler
    {
        if (! isset($this->handler)) {
            $this->handler = app(SharpImpersonationHandler::class);
        }

        return $this->handler;
    }
}
