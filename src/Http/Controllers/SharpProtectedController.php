<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Routing\Controller;

class SharpProtectedController extends Controller
{
    protected SharpEntityManager $entityManager;
    protected SharpAuthorizationManager $authorizationManager;

    public function __construct()
    {
        $this->entityManager = app(SharpEntityManager::class);
        $this->authorizationManager = app(SharpAuthorizationManager::class);

        $guardSuffix = sharp()->config()->get('auth.guard') ? ':'.sharp()->config()->get('auth.guard') : '';
        $this->middleware('sharp_auth'.$guardSuffix);
    }
}
