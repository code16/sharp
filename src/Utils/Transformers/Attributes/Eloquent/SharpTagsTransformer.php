<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Contracts\Support\Arrayable;

class SharpTagsTransformer implements SharpAttributeTransformer
{
    protected ?string $linkEntityKey = null;
    protected ?string $linkFilter = null;
    protected ?string $linkIdAttribute = null;

    public function __construct(
        protected string $labelAttribute,
        protected ?int $labelLimit = 30,
    ) {
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
        
        return view('sharp::transformers.tags', [
            'tags' => collect($instance->$attribute)
                ->map(fn ($tag) => [
                    'label' => $tag->{$this->labelAttribute},
                    'url' => $this->linkEntityKey
                        ? LinkToEntityList::make($this->linkEntityKey)
                            ->addFilter($this->linkFilter, $tag->{$this->linkIdAttribute})
                            ->renderAsUrl()
                        : null
                ]),
            'labelLimit' => $this->labelLimit,
        ])->render();
    }
}
