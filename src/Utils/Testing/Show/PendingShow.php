<?php

namespace Code16\Sharp\Utils\Testing\Show;

use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\Commands\FormatsDataForCommand;
use Code16\Sharp\Utils\Testing\Commands\PendingCommand;
use Code16\Sharp\Utils\Testing\Dashboard\PendingDashboard;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\Form\PendingForm;
use Code16\Sharp\Utils\Testing\GeneratesGlobalFilterUrl;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Illuminate\Foundation\Testing\TestCase;

class PendingShow
{
    use FormatsDataForCommand;
    use GeneratesGlobalFilterUrl;
    use IsPendingComponent;

    public SharpShow $show;
    public string $entityKey;

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        public string|int|null $instanceId = null,
        public PendingEntityList|PendingShow|null $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->show = app(SharpEntityManager::class)->entityFor($this->entityKey)->getShowOrFail();
    }

    public function sharpForm(string $entityClassNameOrKey): PendingForm
    {
        return new PendingForm($this->test, $entityClassNameOrKey, $this->instanceId, parent: $this);
    }

    public function sharpListField(string $entityClassNameOrKey): PendingEntityList
    {
        return new PendingEntityList($this->test, $entityClassNameOrKey, parent: $this);
    }

    public function sharpDashboardField(string $entityClassNameOrKey): PendingDashboard
    {
        return new PendingDashboard($this->test, $entityClassNameOrKey, parent: $this);
    }

    public function get(): AssertableShow
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableShow(
            $this->test
                ->get($this->show instanceof SharpSingleShow
                    ? route('code16.sharp.single-show', [
                        'entityKey' => $this->entityKey,
                    ])
                    : route('code16.sharp.show.show', [
                        'parentUri' => $this->getParentUri(),
                        'entityKey' => $this->entityKey,
                        'instanceId' => $this->instanceId,
                    ])
                )
        );
    }

    public function instanceCommand(string $commandKeyOrClassName): PendingCommand
    {
        $this->setGlobalFilterUrlDefault();

        $commandKey = class_exists($commandKeyOrClassName)
            ? class_basename($commandKeyOrClassName)
            : $commandKeyOrClassName;

        return new PendingCommand(
            getForm: fn (?string $step = null) => $this
                ->test
                ->getJson(
                    $this->show instanceof SharpSingleShow
                        ? route(
                            'code16.sharp.api.show.command.singleInstance.form',
                            ['entityKey' => $this->entityKey, 'commandKey' => $commandKey, 'command_step' => $step]
                        )
                        : route(
                            'code16.sharp.api.show.command.instance.form',
                            [
                                'entityKey' => $this->entityKey,
                                'instanceId' => $this->instanceId,
                                'commandKey' => $commandKey,
                                'command_step' => $step,
                            ]
                        ),
                    headers: [
                        SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => $this->getCurrentPageUrlFromParents(),
                    ]
                ),
            post: fn (?array $data, ?string $step = null, ?array $baseData = null) => $this
                ->test
                ->postJson(
                    route(
                        'code16.sharp.api.show.command.instance',
                        ['entityKey' => $this->entityKey, 'instanceId' => $this->instanceId, 'commandKey' => $commandKey]
                    ),
                    [
                        'data' => $this->formatDataForCommand($this->show->findInstanceCommandHandler($commandKey), $data, $baseData),
                        'command_step' => $step,
                    ],
                    headers: [
                        SharpBreadcrumb::CURRENT_PAGE_URL_HEADER => $this->getCurrentPageUrlFromParents(),
                    ]
                ),
            commandContainer: $this->show,
        );
    }
}
