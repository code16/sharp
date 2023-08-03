<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Auth\Authenticatable;

final class UserData extends Data
{
    public function __construct(
        public ?string $name,
    ) {
    }

    public static function from(Authenticatable $user): self
    {
        return new self(
            name: $user->{config('sharp.auth.display_attribute', 'name')} ?? null,
        );
    }
}
