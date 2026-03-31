<?php

namespace Code16\Sharp\Http\Controllers\Auth\Passkeys;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class PasskeySkipPromptController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect()
            ->to(route('code16.sharp.home'))
            ->withCookie(cookie()->forever('sharp_skip_passkey_prompt', true));
    }
}
