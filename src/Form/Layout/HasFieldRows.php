<?php

namespace Code16\Sharp\Form\Layout;

trait HasFieldRows
{
    /**
     * @var array
     */
    protected $rows = [];

    /**
     * @param string $fieldKey
     * @param \Closure|null $subLayoutCallback
     * @return $this
     */
    public function withSingleField(string $fieldKey, \Closure $subLayoutCallback = null)
    {
        $this->addRowLayout([
            new FormLayoutField($fieldKey, $subLayoutCallback)
        ]);

        return $this;
    }

    /**
     * @param string ...$fieldKeys
     * @return $this
     */
    public function withFields(string ...$fieldKeys)
    {
        $this->addRowLayout(collect($fieldKeys)->map(function($key) {
            return new FormLayoutField($key);
        })->all());

        return $this;
    }

    /**
     * @param array $fields
     */
    private function addRowLayout(array $fields)
    {
        $this->rows[] = $fields;
    }

    /**
     * @return array
     */
    function fieldsToArray(): array
    {
        return [
            "fields" => collect($this->rows)
                ->map(function($row) {
                    return collect($row)
                        ->map(function($field) {
                            return $field->toArray();
                        })
                        ->all();
                })
                ->all()
        ];
    }
}