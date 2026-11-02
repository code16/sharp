<?php

namespace Code16\Sharp\Http\Controllers\Auth;

use Code16\Sharp\Auth\Impersonate\SharpImpersonationHandler;
use Code16\Sharp\Http\Controllers\Auth\Concerns\RedirectsToIntendedUrl;
use Code16\Sharp\Http\Controllers\Auth\Requests\ImpersonateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ImpersonateController extends Controller
{
    use RedirectsToIntendedUrl;

    public function create(?SharpImpersonationHandler $impersonationHandler): RedirectResponse|Response
    {
        return Inertia::render('Auth/Impersonate', [
            'impersonateUsers' => $impersonationHandler?->enabled()
                ? $impersonationHandler->getUsers()
                : null,
        ]);
    }

    public function store(ImpersonateRequest $request): SymfonyResponse
    {
        auth(sharp()->config()->get('auth.guard'))
            ->loginUsingId($request->input('user_id'));

        $request->session()->regenerate();

        return $this->redirectToIntendedUrl();
    }
}
