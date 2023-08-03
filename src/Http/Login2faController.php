<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Auth\TwoFactor\Sharp2faHandler;
use Code16\Sharp\Http\Requests\Login2faRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class Login2faController extends Controller
{
    public function create(Sharp2faHandler $sharp2faService): RedirectResponse|Response
    {
        if ($sharp2faService->isExpectingLogin()) {
            $helpText = method_exists($sharp2faService, 'formHelpText')
                ? $sharp2faService->formHelpText()
                : trans('sharp::auth.2fa.form_help_text');

            return Inertia::render('Auth/Login2Fa', [
                'helpText' => $helpText,
            ]);
        }

        return redirect()->route('code16.sharp.login');
    }

    public function store(Login2faRequest $request, Sharp2faHandler $sharp2faService): RedirectResponse
    {
        $request->authenticate($sharp2faService);
        $request->session()->regenerate();

        return redirect()->intended(route('code16.sharp.home'));
    }
}
