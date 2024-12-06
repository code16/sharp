<?php

namespace Code16\Sharp\Exceptions\Form;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SharpApplicativeException extends SharpException
{
    public function getStatusCode(): int
    {
        return 417;
    }
    
    public function render(Request $request): Response|RedirectResponse|JsonResponse|bool
    {
        if($request->routeIs('code16.sharp.form.store', 'code16.sharp.form.update')) {
            throw ValidationException::withMessages(['error' => $this->getMessage()]);
        }
        
        return parent::render($request);
    }
}
