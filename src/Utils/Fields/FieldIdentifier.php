<?php

namespace Code16\Sharp\Utils\Fields;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Show\Fields\SharpShowField;
use Illuminate\Support\Facades\Crypt;

class FieldIdentifier
{
    protected string $fieldKey;

    public function __construct(
        public string $container,
        public array $data,
    ) {}

    public function isEmbed(): bool
    {
        return $this->container === 'embed';
    }

    public function forField(SharpFormField|SharpShowField $field): self
    {
        $identifier = clone $this;
        $identifier->fieldKey = $field->key();

        return $identifier;
    }

    public static function decrypt(string $encrypted): self
    {
        return Crypt::decrypt($encrypted);
    }

    public function encrypt(): string
    {
        return Crypt::encrypt($this);
    }
}
