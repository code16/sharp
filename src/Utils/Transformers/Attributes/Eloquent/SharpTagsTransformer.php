<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Utils\Links\LinkToEntityList;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

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
        
        $tags = $instance->$attribute
            ->map(fn ($tag) => $this->renderTag($tag))
            ->join('');
        
        return <<<HTML
            <div class="flex gap-2 flex-wrap">
                $tags
            </div>
        HTML;
    }

    protected function renderTag(object $tag): string
    {
        $label = $tag->{$this->labelAttribute};
        
        if($this->labelLimit) {
            $limitedLabel = Str::limit($label, $this->labelLimit);
        }

        if($this->linkEntityKey) {
            $url = LinkToEntityList::make($this->linkEntityKey)
                ->addFilter($this->linkFilter, $tag->{$this->linkIdAttribute})
                ->renderAsUrl();
        }
        
        return Blade::render(<<<'HTML'
            <x-sharp::badge :href="$url" :title="$title">{{ $label }}</x-sharp::badge>
        HTML, [
            'label' => $limitedLabel ?? $label,
            'title' => isset($limitedLabel) && strlen($label) > strlen($limitedLabel) ? $label : null,
            'url' => $url ?? null,
        ]);
    }
}
