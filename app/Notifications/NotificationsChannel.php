<?php

namespace App\Notifications;

use Illuminate\Notifications\Channels\DatabaseChannel as IlluminateDatabaseChannel;

class NotificationsChannel extends IlluminateDatabaseChannel
{
    /**
     * 指定された通知を送信
     *
     * @param mixed $notifiable
     * @param object $notification
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function send($notifiable, object $notification)
    {
        return $notifiable->routeNotificationFor('database', $notification)->create([
            'id' => $notification->id,
            'type' => method_exists($notification, 'databaseType')
                ? $notification->databaseType($notifiable)
                : get_class($notification),
            'category_id' => $notification->category_id,
            'category' => $notification->category,
            'data' => $this->getData($notifiable, $notification),
            'read_at' => null
        ]);
    }
}
