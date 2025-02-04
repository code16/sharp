<?php

namespace Code16\Sharp\Utils\Traits;

use Code16\Sharp\Utils\SharpNotification;

trait CanNotify
{
    public function notify(string $title): SharpNotification
    {
        return new SharpNotification($title);
    }
}
