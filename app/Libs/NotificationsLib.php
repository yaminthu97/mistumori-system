<?php

namespace App\Libs;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Pusher\Pusher;
use App\Models\User;
use App\Notifications\Notifications;

class NotificationsLib
{
    use Notifiable;

    /**
     * 通知を保存
     *
     * @param array $notification_receivers
     * @param object $notification_data
     * @param string $notification_category
     * @return void
     */
    public function saveNotifications(array $notification_receivers, object $notification_data, string $notification_category): void
    {
        $user = Auth::user();
        $notification = new Notifications($user, $notification_data, $notification_category);

        if (in_array($user->id, $notification_receivers)) {
            $notification_receivers = array_filter($notification_receivers, function ($item) use ($user) {
                return $item != $user->id || null;
            });
        }

        $notification_receivers = array_unique($notification_receivers);

        foreach ($notification_receivers as $receiver_id) {
            $receiver = User::where('id', $receiver_id)->first();
            if ($receiver) {
                $receiver->notify($notification);
            }
        }
    }

    /**
     * すべての通知を取得
     *
     * @return object
     */
    public function getAllNotifications(): object
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', Auth::user()->id)
            ->orderByDesc('created_at')
            ->get();
        return $query;
    }

    /**
     * すべての通知のカウントを取得
     *
     * @return object
     */
    public function getAllNotificationsCount(): object
    {
        $query = DB::table('notifications')
            ->where('notifiable_id', Auth::user()->id)
            ->whereNull('read_at')
            ->get();
        return $query;
    }

    /**
     * プッシャー設定
     *
     * @param  $request
     * @return void
     */
    public function pusherNotification(): void
    {
        $options = array(
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        );
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );
        $data = ['target' => 'notification'];
        // 前面に通知を送信する
        $pusher->trigger('notification-channel', 'notification-event', $data);
    }

    /**
     * IDによる通知を読む
     *
     * @param type $id
     * @return object
     */
    public function readNotificationById($id): object
    {
        $notifications = Auth::user()->notifications->find($id);
        DB::table('notifications')->where('id', $notifications->id)->update(['read_at' => now()]);
        return $notifications;
    }
}
