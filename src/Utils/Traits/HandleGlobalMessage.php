<?php

namespace Code16\Sharp\Utils\Traits;

use Code16\Sharp\Show\Fields\SharpShowHtmlField;

trait HandleGlobalMessage
{
    protected ?SharpShowHtmlField $globalMessageHtmlField = null;

    protected function setGlobalMessage(string $template, string $fieldKey = null, bool $declareTemplateAsPath = false): self
    {
        $this->globalMessageHtmlField = SharpShowHtmlField::make($fieldKey ?: uniqid("f"));

        if($declareTemplateAsPath) {
            $this->globalMessageHtmlField->setTemplatePath($template);
        } else {
            $this->globalMessageHtmlField->setInlineTemplate($template);
        }

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendGlobalMessageToConfig(array &$config): void
    {
        if($this->globalMessageHtmlField) {
            $config["globalMessage"] = [
                "fieldKey" => $this->globalMessageHtmlField->key,
                "alertLevel" => 'primary'
            ];
        }
    }
}
