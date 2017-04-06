<?php

namespace Code16\Sharp\Form\Fields;

use Code16\Sharp\Form\Fields\Utils\WithPlaceholder;

class SharpFormTextareaField extends SharpFormField
{
    use WithPlaceholder;

    /**
     * @var int
     */
    protected $rows;

    /**
     * @param string $key
     * @return static
     */
    public static function make(string $key)
    {
        return new static($key, 'textarea');
    }

    /**
     * @param int $rows
     * @return $this
     */
    public function setRowCount(int $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return array
     */
    protected function validationRules()
    {
        return [
            "rows" => "integer|min:1",
        ];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return parent::buildArray([
            "rows" => $this->rows,
            "placeholder" => $this->placeholder,
        ]);
    }
}