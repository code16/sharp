<?php

namespace Code16\Sharp\Utils\PageAlerts;

use Code16\Sharp\Enums\PageAlertLevel;

class PageAlert
{
    protected PageAlertLevel $pageAlertLevel = PageAlertLevel::Info;
    protected string $text;

    public function setLevelInfo(): self
    {
        $this->pageAlertLevel = PageAlertLevel::Info;

        return $this;
    }

    public function setLevelWarning(): self
    {
        $this->pageAlertLevel = PageAlertLevel::Warning;

        return $this;
    }

    public function setLevelDanger(): self
    {
        $this->pageAlertLevel = PageAlertLevel::Danger;

        return $this;
    }

    public function setLevelPrimary(): self
    {
        $this->pageAlertLevel = PageAlertLevel::Primary;

        return $this;
    }

    public function setLevelSecondary(): self
    {
        $this->pageAlertLevel = PageAlertLevel::Secondary;

        return $this;
    }

    public function setMessage(string $message): self
    {
        $this->text = $message;

        return $this;
    }

    public final function toArray(): ?array
    {
        return $this->isFilled()
            ? [
                'level' => $this->pageAlertLevel->value,
                'text' => $this->text,
            ]
            : null;
    }

    private function isFilled(): bool
    {
        return str($this->text ?? null)->trim()->isNotEmpty();
    }
}