<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\Formatters\SharpFieldFormatter;
use Illuminate\Support\Facades\Validator;

abstract class SharpFormField
{
    public string $key;
    protected ?string $label = null;
    protected string $type;
    protected ?string $helpMessage = null;
    protected string $conditionalDisplayOperator = 'and';
    protected array $conditionalDisplayFields = [];
    protected ?bool $readOnly = null;
    protected ?string $extraStyle = null;
    protected ?SharpFieldFormatter $formatter;

    protected function __construct(string $key, string $type, ?SharpFieldFormatter $formatter = null)
    {
        $this->key = $key;
        $this->type = $type;
        $this->formatter = $formatter;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function setHelpMessage(string $helpMessage): self
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }

    public function addConditionalDisplay(string $fieldKey, array|string|bool $values = true): self
    {
        if (str_starts_with($fieldKey, '!')) {
            $fieldKey = substr($fieldKey, 1);
            $values = false;
        }

        $this->conditionalDisplayFields[] = [
            'key' => $fieldKey,
            'values' => $values,
        ];

        return $this;
    }

    public function setConditionalDisplayOrOperator(): self
    {
        $this->conditionalDisplayOperator = 'or';

        return $this;
    }

    public function setConditionalDisplayAndOperator(): self
    {
        $this->conditionalDisplayOperator = 'and';

        return $this;
    }

    public function setReadOnly(bool $readOnly = true): self
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    public function setExtraStyle(string $style): self
    {
        $this->extraStyle = $style;

        return $this;
    }

    public function setFormatter(SharpFieldFormatter $formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    /**
     * Create the properties array for the field, using parent::buildArray().
     */
    abstract public function toArray(): array;

    public function type(): string
    {
        return $this->type;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function formatter(): ?SharpFieldFormatter
    {
        return $this->formatter;
    }

    protected function validationRules(): array
    {
        return [];
    }

    /**
     * Throw an exception in case of invalid attribute value.
     *
     *
     * @throws SharpFormFieldValidationException
     */
    protected function validate(array $properties)
    {
        $validator = Validator::make(
            $properties,
            array_merge(
                [
                    'key' => 'required',
                    'type' => 'required',
                ],
                $this->validationRules()
            )
        );

        if ($validator->fails()) {
            throw new SharpFormFieldValidationException($validator->errors());
        }
    }

    protected function buildArray(array $childArray): array
    {
        $array = collect(
            [
                'key' => $this->key,
                'type' => $this->type,
                'label' => $this->label,
                'readOnly' => $this->readOnly,
                'conditionalDisplay' => $this->buildConditionalDisplayArray(),
                'helpMessage' => $this->helpMessage,
                'extraStyle' => $this->extraStyle,
            ])
            ->merge($childArray)
            ->filter(fn ($value) => $value !== null)
            ->all();

        $this->validate($array);

        return $array;
    }

    private function buildConditionalDisplayArray(): ?array
    {
        if (! count($this->conditionalDisplayFields)) {
            return null;
        }

        return [
            'operator' => $this->conditionalDisplayOperator,
            'fields' => $this->conditionalDisplayFields,
        ];
    }
}
