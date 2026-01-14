<?php

namespace Code16\Sharp\Utils\Testing\Form;

use Code16\Sharp\Utils\Testing\DelegatesToResponse;
use Illuminate\Testing\TestResponse;

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

    public function formData(): array
    {
        return $this->response->inertiaProps('form.data');
    }
}
