<?php

namespace Code16\Sharp\Events;

class FileSaved
{
    public function __construct(
        public string $path,
        public string $disk,
    ) {}
}
