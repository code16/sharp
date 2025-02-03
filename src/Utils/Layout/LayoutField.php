<?php

namespace Code16\Sharp\Utils\Layout;

use Closure;

abstract class LayoutField
{
    protected string $fieldKey;
    protected ?int $size = null;
    protected array $itemLayout = [];

    public function __construct(string|array $fieldKey, ?Closure $subLayoutCallback = null)
    {
        $size = null;

        if (is_array($fieldKey)) {
            [$this->fieldKey, $size] = $fieldKey;
        } elseif (strpos($fieldKey, '|')) {
            [$this->fieldKey, $size] = explode('|', $fieldKey);
        } else {
            $this->fieldKey = $fieldKey;
        }

        $this->size = $size ? (int) $size : null;

        if ($subLayoutCallback) {
            $itemFormLayout = $this->getLayoutColumn();
            $subLayoutCallback($itemFormLayout);
            $this->itemLayout = $itemFormLayout->toArray()['fields'];
        }
    }

    abstract protected function getLayoutColumn(): LayoutColumn;

    public function toArray(): array
    {
        return array_merge(
            [
                'key' => $this->fieldKey,
                'size' => $this->size ?: 12,
            ],
            $this->itemLayout
                ? ['item' => $this->itemLayout]
                : [],
        );
    }
}
