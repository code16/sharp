<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\CommandAction;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Types\StructType;

// download & streamDownload actions returns the file directly in the response
#[LiteralTypeScriptType(
    '{ action: "'.CommandAction::Link->value.'", link: string } | '.
    '{ action: "'.CommandAction::Info->value.'", message: string } | '.
    '{ action: "'.CommandAction::Refresh->value.'", items?: Array<number | string> } | '.
    '{ action: "'.CommandAction::Reload->value.'" } | '.
    '{ action: "'.CommandAction::Step->value.'", step: string } | '.
    '{ action: "'.CommandAction::View->value.'", html: string }'
)]
final class CommandReturnData extends Data
{
    public function __construct(
        public CommandAction $action,
    ) {
    }
}
