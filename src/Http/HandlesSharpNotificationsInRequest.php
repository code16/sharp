<?php

namespace Code16\Sharp\Http;

trait HandlesSharpNotificationsInRequest
{
    protected function getSharpNotifications(): array
    {
        if ($notifications = session('sharp_notifications')) {
            session()->forget('sharp_notifications');

            return array_values($notifications);
        }

        return [];
    }
}