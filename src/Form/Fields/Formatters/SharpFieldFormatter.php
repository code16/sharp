<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Http\Jobs\CurrentRequestJobs;

abstract class SharpFieldFormatter
{
    protected ?string $instanceId = null;
    protected ?array $dataLocalizations = null;
    protected ?CurrentRequestJobs $jobQueue = null;

    public function setInstanceId(?string $instanceId): static
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    public function setDataLocalizations(array $dataLocalizations): static
    {
        $this->dataLocalizations = $dataLocalizations;

        return $this;
    }
    
    public function setJobQueue(CurrentRequestJobs $jobQueue): static
    {
        $this->jobQueue = $jobQueue;
        
        return $this;
    }

    abstract public function toFront(SharpFormField $field, $value);

    abstract public function fromFront(SharpFormField $field, string $attribute, $value);
}
