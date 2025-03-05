<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class DashboardEntity extends SharpDashboardEntity
{
    public static string $entityKey = 'dashboard';
    protected ?string $view = TestDashboard::class;
    protected ?SharpDashboard $fakeView = null;
    protected ?SharpEntityPolicy $fakePolicy = null;

    public function setShow(?SharpDashboard $show): self
    {
        $this->fakeView = $show;

        return $this;
    }

    protected function getView(): SharpDashboard
    {
        return $this->fakeView ?? parent::getView();
    }

    public function setPolicy(SharpEntityPolicy $policy): self
    {
        $this->fakePolicy = $policy;

        return $this;
    }

    protected function getPolicy(): ?SharpEntityPolicy
    {
        return $this->fakePolicy ?? parent::getPolicy();
    }
}
