<?php

namespace App\Sharp\Entities;

use App\Sharp\Dashboard\PostDashboard;
use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class PostDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = PostDashboard::class;

    protected function getPolicy(): ?SharpEntityPolicy
    {
        return new class() extends SharpEntityPolicy
        {
            public function entity($user): bool
            {
                return $user->isAdmin();
            }
        };
    }
}
