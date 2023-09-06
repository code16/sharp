<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;

final class FormData extends Data
{
    public function __construct(
        public FormConfigData $config,
        /** @var array<string,mixed> */
        public array $data,
    ) {
    }

    public static function from(array $form): self
    {
        return new self(
            config: FormConfigData::from($form['config']),
            data: $form['data'],
        );
    }
}
