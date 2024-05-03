<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Contracts\Support\Arrayable;

class SharpTagsTransformer implements SharpAttributeTransformer
{
    const tagStyle = 'background-color: #f0f0f0; color: #333; padding: 2px 5px; border-radius: 3px; margin-right: 5px;';

    protected ?string $linkEntityKey = null;
    protected ?string $linkFilter = null;
    protected ?string $linkIdAttribute = null;

    public function __construct(protected string $labelAttribute)
    {
    }

    public function setFilterLink(string $entityKey, string $filter, ?string $idAttribute = 'id'): self
    {
        $this->linkEntityKey = $entityKey;
        $this->linkFilter = $filter;
        $this->linkIdAttribute = $idAttribute;

        return $this;
    }

    public function apply($value, $instance = null, $attribute = null)
    {
        if (! $instance->$attribute) {
            return null;
        }

        if (! $instance->$attribute instanceof Arrayable) {
            throw new SharpException("[$attribute] must be an array");
        }

        return $instance->$attribute
            ->map(fn ($tag) => $this->renderTag($tag))
            ->join('');
    }

    protected function renderTag(object $tag): string
    {
        $label = $tag->{$this->labelAttribute};

        if($this->linkEntityKey) {
            $label = LinkToEntityList::make($this->linkEntityKey)
                ->addFilter($this->linkFilter, $tag->{$this->linkIdAttribute})
                ->renderAsText($label);
        }

        return sprintf(
            '<span style="%s">%s</span>',
            static::tagStyle,
            $label
        );
    }
}
