<?php

namespace Code16\Sharp\Utils\Traits;

use Code16\Sharp\Show\Fields\SharpShowHtmlField;

trait HandlePageAlertMessage
{
    protected ?SharpShowHtmlField $pageAlertHtmlField = null;
    protected string $pageAlertLevel;

    protected static string $pageAlertLevelNone = 'none';
    protected static string $pageAlertLevelInfo = 'info';
    protected static string $pageAlertLevelWarning = 'warning';
    protected static string $pageAlertLevelDanger = 'danger';
    protected static string $pageAlertLevelPrimary = 'primary';
    protected static string $pageAlertLevelSecondary = 'secondary';

    protected function configurePageAlert(string $template, string $alertLevel = null, string $fieldKey = null, bool $declareTemplateAsPath = false): self
    {
        $this->pageAlertHtmlField = SharpShowHtmlField::make($fieldKey ?: uniqid('f'));
        $this->pageAlertLevel = $alertLevel ?? static::$pageAlertLevelNone;

        if ($declareTemplateAsPath) {
            $this->pageAlertHtmlField->setTemplatePath($template);
        } else {
            $this->pageAlertHtmlField->setInlineTemplate($template);
        }

        return $this;
    }

    /**
     * Append the commands to the config returned to the front.
     */
    protected function appendGlobalMessageToConfig(array &$config): void
    {
        if ($this->pageAlertHtmlField) {
            $config['globalMessage'] = [
                'fieldKey'   => $this->pageAlertHtmlField->key,
                'alertLevel' => $this->pageAlertLevel !== static::$pageAlertLevelNone
                    ? $this->pageAlertLevel
                    : null,
            ];
        }
    }
}
