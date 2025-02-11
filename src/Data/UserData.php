<?php

namespace Code16\Sharp\Data;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * @internal
 */
final class UserData extends Data
{
    public function __construct(
        public ?string $name,
        public ?string $email,
        public ?string $avatar,
    ) {}

    public static function from(Authenticatable $user): self
    {
        return new self(
            name: $user->{sharp()->config()->get('auth.display_attribute')} ?? null,
            email: $user->{sharp()->config()->get('auth.login_attribute')} ?? null,
            avatar: $user->{sharp()->config()->get('auth.avatar_attribute')} ?? null,
        );
    }
}
