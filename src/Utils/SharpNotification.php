<?php

namespace Code16\Sharp\Utils;

class SharpNotification
{
    protected string $id;

    public function __construct(string $title)
    {
        $this->id = uniqid();
        $notifications = session('sharp_notifications') ?? [];

        $notifications[$this->id] = [
            'title'    => $title,
            'level'    => 'info',
            'message'  => null,
            'autoHide' => true,
        ];

        session()->put('sharp_notifications', $notifications);
    }

    public function setDetail(string $detail): self
    {
        return $this->update([
            'message' => $detail,
        ]);
    }

    public function setLevelSuccess(): self
    {
        return $this->update([
            'level' => 'success',
        ]);
    }

    public function setLevelInfo(): self
    {
        return $this->update([
            'level' => 'info',
        ]);
    }

    public function setLevelWarning(): self
    {
        return $this->update([
            'level' => 'warning',
        ]);
    }

    public function setLevelDanger(): self
    {
        return $this->update([
            'level' => 'danger',
        ]);
    }

    public function setAutoHide(bool $autoHide = true): self
    {
        return $this->update([
            'autoHide' => $autoHide,
        ]);
    }

    protected function update(array $updatedArray): self
    {
        $notifications = session('sharp_notifications');
        $notifications[$this->id] = array_merge($notifications[$this->id], $updatedArray);

        session()->put('sharp_notifications', $notifications);

        return $this;
    }
}
