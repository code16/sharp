<?php

namespace Code16\Sharp\Form\Fields\Editor\TextInputReplacement\Concerns;

use Code16\Sharp\Form\Fields\Editor\TextInputReplacement\EditorTextInputReplacement;

/**
 * @internal
 */
trait ReplacesFrench
{
    public static function frenchTypography(
        ?string $locale = null,
        bool $nbsp = true,
        bool $guillemets = false,
    ): self {
        return (new self())
            ->when($nbsp)->add(new EditorTextInputReplacement('/( )[!?:;»]/', ' ', $locale))
            ->when($guillemets)->add(new EditorTextInputReplacement('/(["«][^\n\S])/', '« ', $locale))
            ->when($guillemets)->add(new EditorTextInputReplacement('/[«][^\n\S][^»]+([^\n\S]")/', ' »', $locale))
            ->when($guillemets)->add(new EditorTextInputReplacement('/[«][^\n\S][^»]+(")/', ' »', $locale));
    }
}
