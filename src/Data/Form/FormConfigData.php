<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\PageAlertConfigData;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class FormConfigData extends Data
{
    public function __construct(
        public bool $hasShowPage,
        public ?string $deleteConfirmationText,
        #[Optional]
        public ?string $breadcrumbAttribute = null,
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }
    
    public static function from(array $config): self
    {
        $config = [
            ...$config,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];
        
        return new self(...$config);
    }
}
