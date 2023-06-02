<?php

namespace Code16\Sharp\Dashboard\Widgets;

use Code16\Sharp\Exceptions\Dashboard\SharpWidgetValidationException;
use Code16\Sharp\Utils\Links\SharpLinkTo;
use Illuminate\Support\Facades\Validator;

abstract class SharpWidget
{
    protected string $key;
    protected string $type;
    protected ?string $title = null;
    protected ?string $link = null;

    protected function __construct(string $key, string $type)
    {
        $this->key = $key;
        $this->type = $type;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setLink(SharpLinkTo $sharpLinkTo): self
    {
        $this->link = $sharpLinkTo->renderAsUrl();

        return $this;
    }

    public function unsetLink(): void
    {
        $this->link = null;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    protected function validate(array $properties): void
    {
        $validator = Validator::make(
            $properties,
            array_merge(
                [
                    'key' => 'required',
                    'type' => 'required',
                ],
                $this->validationRules(),
            ),
        );

        if ($validator->fails()) {
            throw new SharpWidgetValidationException($validator->errors());
        }
    }

    protected function buildArray(array $childArray): array
    {
        $array = collect()
            ->merge([
                'key' => $this->key,
                'type' => $this->type,
                'title' => $this->title,
                'link' => $this->link,
            ])
            ->merge($childArray)
            ->filter(fn ($value) => $value !== null)
            ->all();

        $this->validate($array);

        return $array;
    }

    protected function validationRules(): array
    {
        return [];
    }

    abstract public function toArray(): array;
}
