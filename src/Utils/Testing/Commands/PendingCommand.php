<?php

namespace Code16\Sharp\Utils\Testing\Commands;

use Closure;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Testing\IsPendingComponent;

class PendingCommand
{
    use IsPendingComponent;

    public function __construct(
        protected Closure $getForm,
        protected Closure $post,
        protected SharpShow|SharpEntityList|SharpDashboard $commandContainer,
        protected ?string $step = null,
    ) {}

    public function getForm(): AssertableCommandForm
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableCommandForm(
            post: $this->post,
            getForm: $this->getForm,
            commandContainer: $this->commandContainer,
            step: $this->step
        );
    }

    public function post(): AssertableCommand
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableCommand(
            postCommand: $this->post,
            getForm: $this->getForm,
            commandContainer: $this->commandContainer,
            step: $this->step
        );
    }
}
