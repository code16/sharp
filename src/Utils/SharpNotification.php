<?php

namespace Code16\Sharp\Utils;

class SharpNotification
{
    /** @var string */
    protected $id;

    /**
     * @param string $title
     */
    public function __construct(string $title)
    {
        $this->id = uniqid();
        $notifications = session("sharp_notifications") ?? [];

        $notifications[$this->id] = [
            "title" => $title,
            "level" => "info",
            "message" => null,
            "autoHide" => true
        ];

        session()->put("sharp_notifications", $notifications);
    }

    /**
     * @param string $detail
     * @return SharpNotification
     */
    public function setDetail(string $detail)
    {
        return $this->update(["message" => $detail]);
    }

    /**
     * @return SharpNotification
     */
    public function setLevelSuccess()
    {
        return $this->update(["level" => "success"]);
    }

    /**
     * @return SharpNotification
     */
    public function setLevelInfo()
    {
        return $this->update(["level" => "info"]);
    }

    /**
     * @return SharpNotification
     */
    public function setLevelWarning()
    {
        return $this->update(["level" => "warning"]);
    }

    /**
     * @return SharpNotification
     */
    public function setLevelDanger()
    {
        return $this->update(["level" => "danger"]);
    }

    /**
     * @param bool $autoHide
     * @return SharpNotification
     */
    public function setAutoHide(bool $autoHide=true)
    {
        return $this->update(["autoHide" => $autoHide]);
    }

    /**
     * @param array $updatedArray
     * @return SharpNotification
     */
    protected function update(array $updatedArray)
    {
        $notifications = session("sharp_notifications");
        $notifications[$this->id] = array_merge($notifications[$this->id], $updatedArray);

        session()->put("sharp_notifications", $notifications);

        return $this;
    }
}