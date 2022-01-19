<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Formatters\DateFormatter;

class SharpFormDateField extends SharpFormField
{
    const FIELD_TYPE = 'date';

    protected bool $hasDate = true;
    protected bool $hasTime = false;
    protected bool $mondayFirst = true;
    protected string $minTime = '00:00';
    protected string $maxTime = '23:59';
    protected int $stepTime = 30;
    protected ?string $displayFormat = null;
    protected ?string $language = null;

    public static function make(string $key): self
    {
        $field = new static($key, static::FIELD_TYPE, new DateFormatter());
        $field->language = app()->getLocale();

        return $field;
    }

    public function setHasDate($hasDate = true): self
    {
        $this->hasDate = $hasDate;

        return $this;
    }

    public function setHasTime($hasTime = true): self
    {
        $this->hasTime = $hasTime;

        return $this;
    }

    public function setMondayFirst(bool $mondayFirst = true): self
    {
        $this->mondayFirst = $mondayFirst;

        return $this;
    }

    public function setSundayFirst(bool $sundayFirst = true): self
    {
        return $this->setMondayFirst(! $sundayFirst);
    }

    public function setMinTime(int $hours, int $minutes = 0): self
    {
        $this->minTime = $this->formatTime($hours, $minutes);

        return $this;
    }

    public function setMaxTime(int $hours, int $minutes = 0): self
    {
        $this->maxTime = $this->formatTime($hours, $minutes);

        return $this;
    }

    public function setStepTime(int $step): self
    {
        $this->stepTime = $step;

        return $this;
    }

    public function setDisplayFormat(string $displayFormat = null): self
    {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    public function hasDate(): bool
    {
        return $this->hasDate;
    }

    public function hasTime(): bool
    {
        return $this->hasTime;
    }

    protected function validationRules(): array
    {
        return [
            'hasDate' => 'required|boolean',
            'hasTime' => 'required|boolean',
            'displayFormat' => 'required',
            'minTime' => 'regex:/[0-9]{2}:[0-9]{2}/',
            'maxTime' => 'regex:/[0-9]{2}:[0-9]{2}/',
            'stepTime' => 'integer|min:1|max:60',
            'mondayFirst' => 'required|boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'hasDate' => $this->hasDate,
            'hasTime' => $this->hasTime,
            'minTime' => $this->minTime,
            'maxTime' => $this->maxTime,
            'stepTime' => $this->stepTime,
            'mondayFirst' => $this->mondayFirst,
            'displayFormat' => $this->displayFormat ?: $this->detectDisplayFormat(),
            'language' => $this->language,
        ]);
    }

    private function formatTime(int $hours, int $minutes): string
    {
        return str_pad($hours, 2, '0', STR_PAD_LEFT)
            .':'
            .str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }

    protected function detectDisplayFormat(): string
    {
        if ($this->hasDate()) {
            if ($this->hasTime()) {
                return 'YYYY-MM-DD HH:mm';
            }

            return 'YYYY-MM-DD';
        }

        return $this->hasTime() ? 'HH:mm' : '';
    }
}
