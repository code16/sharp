<?php

namespace Code16\Sharp\Tests\Fixtures;

use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class PersonalDashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = SharpDashboard::class;

    public function setView(string $view): self
    {
        $this->view = $view;

        return $this;
    }

    public function setPolicy(string $policy): void
    {
        $this->policy = $policy;
    }
}
