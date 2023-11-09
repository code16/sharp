<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\CommandAction;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

// download & streamDownload actions returns the file directly in the response
#[LiteralTypeScriptType(
    '{ action: "'.CommandAction::Link->value.'", link: string } | '.
    '{ action: "'.CommandAction::Info->value.'", message: string } | '.
    '{ action: "'.CommandAction::Refresh->value.'", items?: Array<number | string> } | '.
    '{ action: "'.CommandAction::Reload->value.'" } | '.
    '{ action: "'.CommandAction::Step->value.'", step: string } | '.
    '{ action: "'.CommandAction::View->value.'", html: string }'
)]
final class CommandResponseData extends Data
{
    public function __construct(
        public CommandAction $action,
    ) {
    }
}
