<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Code16\Sharp\Auth\TwoFactor\MultiFactorManager;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PasskeyController extends Controller
{
    public function create(Request $request, MultiFactorManager $multiFactor): Response
    {
        return Inertia::render('Auth/Passkeys/Create', [
            'prompt' => $request->boolean('prompt'),
            'isMultiFactor' => $multiFactor->isUsingPasskeyMethod(),
            'cancelUrl' => redirect()->getIntendedUrl(),
        ]);
    }
}
