<?php

namespace App\Services;

use App\Models\AdminNotification;
use Illuminate\Support\Arr;

class NotificationService
{
    /**
     * Create a notification for admin(s).
     *
     * @param string $type   // e.g. 'kyc.updated'
     * @param string $title  // short title shown in list
     * @param array  $data   // extra data
     * @param string|null $link // redirect url for admin
     * @param int|null $actorId // user who caused event
     *
     * @return AdminNotification
     */
    public static function create(string $type, string $title, array $data = [], ?string $link = null, ?int $actorId = null): AdminNotification
    {
        $notification = AdminNotification::create([
            'type' => $type,
            'title' => $title,
            'data' => $data,
            'link' => $link,
            'actor_id' => $actorId,
        ]);

        // optional: broadcast event for real-time update (if you use broadcasting)
        // event(new \App\Events\AdminNotificationCreated($notification));

        return $notification;
    }
}