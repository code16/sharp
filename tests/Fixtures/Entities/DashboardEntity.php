<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;

class DashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = TestDashboard::class;
    protected ?SharpDashboard $fakeView = null;

    public function setShow(?SharpDashboard $show): self
    {
        $this->fakeView = $show;

        return $this;
    }

    protected function getView(): SharpDashboard
    {
        return $this->fakeView ?? parent::getView();
    }
}