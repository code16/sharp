<?php

namespace Code16\Sharp\Utils\Testing\Show;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\AssertableCommand;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\GeneratesSharpUrl;
use Illuminate\Foundation\Testing\TestCase;

class PendingShow
{
    use GeneratesSharpUrl;

    protected SharpEntityList $entityList;
    protected string $entityKey;

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        protected string|int|null $instanceId = null,
        protected PendingEntityList|PendingShow|null $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->entityList = app(SharpEntityManager::class)->entityFor($this->entityKey)->getShowOrFail();
    }

    public function callInstanceCommand(
        string $commandKeyOrClassName,
        array $data = [],""
        ?string $commandStep = null,
    ): AssertableCommand {
        $this->setGlobalFilterUrlDefault();

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return new AssertableCommand(
            fn ($data, $step) => $this
                ->test
                ->withHeader(
                    SharpBreadcrumb::CURRENT_PAGE_URL_HEADER,
                    $this->buildCurrentPageUrl(
                        $this->breadcrumbBuilder($this->entityKey, $this->instanceId)
                    ),
                )
                ->postJson(
                    route(
                        'code16.sharp.api.show.command.instance',
                        ['entityKey' => $this->entityKey, 'instanceId' => $this->instanceId, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $data,
                        'command_step' => $step,
                    ],
                ),
            commandContainer: $this->entityList,
            data: $data,
            step: $commandStep,
        );
    }
}
