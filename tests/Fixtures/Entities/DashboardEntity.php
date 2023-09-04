<?php

namespace Code16\Sharp\Tests\Fixtures\Entities;

use Closure;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Entities\SharpDashboardEntity;
use Code16\Sharp\Utils\Entities\SharpEntity;

class DashboardEntity extends SharpDashboardEntity
{
    protected ?string $view = TestDashboard::class;
}