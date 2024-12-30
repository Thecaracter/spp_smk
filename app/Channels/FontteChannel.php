<?php

namespace App\Channels;

use App\Services\FontteService;
use Illuminate\Notifications\Notification;

class FontteChannel
{
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'toFonnte')) {
            return $notification->toFonnte($notifiable);
        }
    }
}