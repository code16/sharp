<?php

namespace App\Sharp\Utils\Filters;

use App\Models\PostAttachment;
use Code16\Sharp\Filters\AutocompleteRemoteFilter;

class PostAttachmentFilter extends AutocompleteRemoteFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Attachment');
    }

    public function values(string $query): array
    {
        return PostAttachment::query()
            ->orderBy('title')
            ->when($query, function ($builder) use ($query) {
                $builder->where('title', 'like', "%$query%");
            })
            ->get()
            ->pluck('title', 'id')
            ->unique()
            ->toArray();
    }

    public function valueLabelFor(string $id): string
    {
        return PostAttachment::find($id)->title ?? '';
    }
}
