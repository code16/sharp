<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class PaginatorMetaData extends Data
{
    public function __construct(
        public int $current_page,
        public string $first_page_url,
        public int $from,
        public ?string $next_page_url,
        public string $path,
        public int $per_page,
        public ?string $prev_page_url,
        public int $to,
        #[LiteralTypeScriptType('Array<{ url: string|null, label: string, active: boolean }>')]
        public ?array $links = null,
        public ?int $last_page = null,
        public ?string $last_page_url = null,
        public ?int $total = null,
    ) {
    }

    /**
     * Filter the Paginator array in case of a new property is added in the future (which will throw).
     */
    public static function from(array $meta): self
    {
        return new self(
            ...Arr::only($meta, [
                'current_page',
                'first_page_url',
                'from',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'links',
                'last_page',
                'last_page_url',
                'total',
            ])
        );
    }
}
