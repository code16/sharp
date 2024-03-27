<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Http\Controllers\Auth\Requests\ImpersonateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ImpersonateController extends Controller
{
    public function __construct()
    {
        $guardSuffix = config('sharp.auth.guard') ? ':'.config('sharp.auth.guard') : '';

        $this->middleware('sharp_guest'.$guardSuffix);
    }

    public function create(SharpImpersonationHandler $impersonationHandler): RedirectResponse|Response
    {
        return Inertia::render('Auth/Impersonate', [
            'impersonateUsers' => $impersonationHandler->enabled()
                ? $impersonationHandler->getUsers()
                : null,
        ]);
    }

    public function store(ImpersonateRequest $request): RedirectResponse
    {
        auth(config('sharp.auth.guard'))
            ->loginUsingId($request->input('user_id'));

        $request->session()->regenerate();

        return redirect()->intended(route('code16.sharp.home'));
    }
}
