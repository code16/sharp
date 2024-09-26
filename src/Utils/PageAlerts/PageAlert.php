<?php

namespace Code16\Sharp\Utils\PageAlerts;

use Closure;
use Code16\Sharp\Enums\PageAlertLevel;

class PageAlert
{
    protected PageAlertLevel $pageAlertLevel = PageAlertLevel::Info;
    protected ?string $text;
    protected array $data;

    final public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function setLevel(PageAlertLevel $level): self
    {
        $this->pageAlertLevel = $level;

        return $this;
    }

    public function setLevelInfo(): self
    {
        return $this->setLevel(PageAlertLevel::Info);
    }

    public function setLevelWarning(): self
    {
        return $this->setLevel(PageAlertLevel::Warning);
    }

    public function setLevelDanger(): self
    {
        return $this->setLevel(PageAlertLevel::Danger);
    }

    public function setLevelPrimary(): self
    {
        return $this->setLevel(PageAlertLevel::Primary);
    }

    public function setLevelSecondary(): self
    {
        return $this->setLevel(PageAlertLevel::Secondary);
    }

    public function setMessage(Closure|string $message): self
    {
        $this->text = $message instanceof Closure
            ? $message($this->data)
            : $message;

        return $this;
    }

    final public function toArray(): ?array
    {
        return $this->isFilled()
            ? [
                'level' => $this->pageAlertLevel,
                'text' => $this->text,
            ]
            : null;
    }

    private function isFilled(): bool
    {
        return str($this->text ?? null)->trim()->isNotEmpty();
    }
}
