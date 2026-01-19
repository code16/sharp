<?php

namespace Code16\Sharp\Utils\Testing\Form;

use Closure;
use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia;

class AssertableForm
{
    use DelegatesToResponse;
    use FormatsDataForUpdate;

    public function __construct(
        protected TestResponse $response,
        protected PendingForm $pendingForm,
    ) {}

    public function store(array $data = []): TestResponse
    {
        return $this->pendingForm->store(
            $this->formatDataForUpdate($this->pendingForm->form, $data, baseData: $this->formData())
        );
    }

    public function update(array $data = []): TestResponse
    {
        return $this->pendingForm->update(
            $this->formatDataForUpdate($this->pendingForm->form, $data, baseData: $this->formData())
        );
    }

    /**
     * @param  Closure(AssertableJson): mixed  $callback
     */
    public function assertFormData(Closure $callback): static
    {
        $this->response->assertInertia(fn (AssertableInertia $page) => $page
            ->has('_rawData', fn (AssertableJson $json) => $callback($json))
            ->etc()
        );

        return $this;
    }

    protected function formData(): array
    {
        return $this->response->inertiaProps('form.data');
    }
}
