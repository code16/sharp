<?php

namespace Code16\Sharp\Utils\Testing\Form;

use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Testing\EntityList\PendingEntityList;
use Code16\Sharp\Utils\Testing\GeneratesGlobalFilterUrl;
use Code16\Sharp\Utils\Testing\IsPendingComponent;
use Code16\Sharp\Utils\Testing\Show\PendingShow;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\TestResponse;

class PendingForm
{
    use FormatsDataForUpdate;
    use GeneratesGlobalFilterUrl;
    use IsPendingComponent;

    public SharpForm $form;
    public string $entityKey;
    protected bool $isSubsequentRequest = false;

    public function __construct(
        /** @var TestCase $test */
        protected object $test,
        string $entityKey,
        public string|int|null $instanceId = null,
        public PendingEntityList|PendingShow|null $parent = null,
    ) {
        $this->entityKey = app(SharpEntityManager::class)->entityKeyFor($entityKey);
        $this->form = app(SharpEntityManager::class)->entityFor($this->entityKey)->getFormOrFail();
    }

    public function create(): AssertableForm
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableForm(
            $this->test
                ->get(route('code16.sharp.form.create', [
                    'parentUri' => $this->getParentUri(),
                    'entityKey' => $this->entityKey,
                ])),
            pendingForm: $this->forSubsequentRequest(),
        );
    }

    public function edit(): AssertableForm
    {
        $this->setGlobalFilterUrlDefault();

        return new AssertableForm(
            $this->test
                ->get(route('code16.sharp.form.edit', [
                    'parentUri' => $this->getParentUri(),
                    'entityKey' => $this->entityKey,
                    'instanceId' => $this->instanceId,
                ])),
            pendingForm: $this->forSubsequentRequest(),
        );
    }

    public function store(array $data): TestResponse
    {
        $this->setGlobalFilterUrlDefault();

        return $this->test
            ->post(
                route('code16.sharp.form.store', [
                    'parentUri' => $this->getParentUri(),
                    'entityKey' => $this->entityKey,
                ]),
                $this->isSubsequentRequest ? $data : $this->formatDataForUpdate($this->form, $data),
            );
    }

    public function update(array $data): TestResponse
    {
        $this->setGlobalFilterUrlDefault();

        return $this->test
            ->post(
                route('code16.sharp.form.update', [
                    'parentUri' => $this->getParentUri(),
                    'entityKey' => $this->entityKey,
                    'instanceId' => $this->instanceId,
                ]),
                $this->isSubsequentRequest ? $data : $this->formatDataForUpdate($this->form, $data),
            );
    }

    protected function forSubsequentRequest(): static
    {
        $pendingForm = clone $this;
        $pendingForm->isSubsequentRequest = true;

        return $pendingForm;
    }
}
