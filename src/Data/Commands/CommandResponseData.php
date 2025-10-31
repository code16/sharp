<?php

namespace Code16\Sharp\Data\Commands;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\CommandAction;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;

// download & streamDownload actions returns the file directly in the response
#[LiteralTypeScriptType(
    '{ action: "'.CommandAction::Link->value.'", link: string, targetBlank: boolean } | '.
    '{ action: "'.CommandAction::Info->value.'", message: string, reload: boolean } | '.
    '{ action: "'.CommandAction::Refresh->value.'", items?: Array<{ [key: string]: any }> } | '.
    '{ action: "'.CommandAction::Reload->value.'" } | '.
    '{ action: "'.CommandAction::Step->value.'", step: string } | '.
    '{ action: "'.CommandAction::View->value.'", html: string }'
)]
/**
 * @internal
 */
final class CommandResponseData extends Data
{
    public function __construct(
        public CommandAction $action,
    ) {}
}
