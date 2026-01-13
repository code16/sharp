<?php

namespace Code16\Sharp\Utils\Testing\Form;

use Code16\Sharp\Show\SharpShow;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\GeneratesGlobalFilterUrl;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;

class PendingForm
{
    use GeneratesGlobalFilterUrl;
    use IsPendingComponent;

    protected SharpShow $show;
    public string $entityKey;

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        protected string|int|null $instanceId = null,
        public PendingEntityList|PendingShow|null $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->show = app(SharpEntityManager::class)->entityFor($this->entityKey)->getShowOrFail();
    }

    public function get(): TestResponse
    {
        $this->setGlobalFilterUrlDefault();

        return $this->test
            ->get(route('code16.sharp.form.edit', [
                'parentUri' => $this->getParentUri(),
                'entityKey' => $this->entityKey,
                'instanceId' => $this->instanceId,
            ]));
    }
}
