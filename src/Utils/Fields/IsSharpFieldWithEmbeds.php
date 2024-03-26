<?php

namespace Code16\Sharp\Utils\Fields;

use Illuminate\Support\Collection;

interface IsSharpFieldWithEmbeds
{
    public function allowEmbeds(array $embeds): self;

    public function embeds(): Collection;
}
