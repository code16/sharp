<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DocVersion
{
    public function __construct(
        public readonly string $slug,
        public readonly bool $latest = false,
    ) {}

    /**
     * @return Collection<int, DocVersion>
     */
    public static function all(): Collection
    {
        return collect(json_decode(file_get_contents(base_path('../docs/versions/config.json')), true))
            ->map(fn (array $version) => new static(
                slug: $version['slug'],
                latest: $version['latest'] ?? false,
            ))
            ->values();
    }

    public static function latest(): static
    {
        $versions = static::all();

        return $versions->first(fn (DocVersion $version) => $version->latest) ?: $versions->first();
    }
}
