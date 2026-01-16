<?php

namespace Code16\Sharp\Utils\Testing\Commands;

use Closure;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Testing\TestResponse;

class AssertableCommandForm
{
    use DelegatesToResponse;

    public function __construct(
        /** @var Closure(array, ?string, ?array): TestResponse */
        protected Closure $post,
        /** @var Closure(?string): TestResponse */
        protected Closure $getForm,
        protected SharpEntityList|SharpShow|SharpDashboard $commandContainer,
        protected ?string $step = null,
    ) {
        $this->response = ($this->getForm)($this->step);
    }

    public function post(array $data = []): AssertableCommand
    {
        return new AssertableCommand(
            postCommand: fn ($data, $step) => ($this->post)($data, $step, $this->formData()),
            getForm: $this->getForm,
            commandContainer: $this->commandContainer,
            data: $data,
            step: $this->step,
        );
    }

    public function formData(): ?array
    {
        return $this->response->json('data');
    }
}
