<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{

    protected $firebase;
    protected $projectId;
    protected $accessToken;

    public function __construct($projectId, $accessToken)
    {
        $this->projectId = $projectId;

        $this->accessToken = $accessToken;

        $this->firebase = new \App\Services\FirebaseJobService(
            $projectId,
            $accessToken
        );
    }


    const TYPE_GENERAL = 'general';

    const TYPE_SOCIETY_JOB = 'society_job';

    const TYPE_GROUP_INVITE = 'group_invite';

    const TYPE_GROUP_JOIN = 'group_join';

    const TYPE_GROUP_LEFT = 'group_left';

    const TYPE_GROUP_REMOVE = 'group_remove';

    const TYPE_GROUP_ADMIN = 'group_admin';

    const TYPE_TRANSFER_OWNER = 'transfer_owner';

    const TYPE_BOOKING = 'booking';

    const TYPE_WALLET = 'wallet';

    const SCREEN_DASHBOARD = 'dashboard';

    const SCREEN_GROUP = 'group_details';

    const SCREEN_SOCIETY_JOB = 'society_jobs';

    const SCREEN_BOOKING = 'booking_details';

    const SCREEN_NOTIFICATION = 'notifications';

    protected function saveNotificationLog(
        $title,
        $body,
        $userId,
        $target,
        $request,
        $response,
        $status
    )
    {

        return DB::table('push_notifications')

            ->insertGetId([

                'title' => $title,

                'body' => $body,

                'sent_by' => 0,

                'user_id' => $userId,

                'route' => null,

                'status' => $status,

                'req_json' => json_encode($request),

                'res_json' => json_encode($response),

                'created_at' => now(),

                'updated_at' => now()

            ]);

    }

    protected function updateNotificationLog(
        $id,
        $status,
        $response
    )
    {

        DB::table('push_notifications')

            ->where('id', $id)

            ->update([

                'status' => $status,

                'res_json' => json_encode($response),

                'updated_at' => now()

            ]);

    }
    
    public function sendToTopic(
        $topic,
        $title,
        $body,
        $type,
        $screen,
        array $data = []
    )
    {
    
        try {
    
            /*
            |--------------------------------------------------------------------------
            | Payload
            |--------------------------------------------------------------------------
            */
    
            $payload = array_merge([
    
                'type' => $type,
    
                'screen' => $screen,
    
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    
            ], $data);
    
            /*
            |--------------------------------------------------------------------------
            | Save Request Log
            |--------------------------------------------------------------------------
            */
    
            $logId = $this->saveNotificationLog(
    
                $title,
    
                $body,
    
                0,
    
                'topic',
    
                [
    
                    'topic' => $topic,
    
                    'payload' => $payload
    
                ],
    
                null,
    
                0
    
            );
    
            /*
            |--------------------------------------------------------------------------
            | Firebase Send
            |--------------------------------------------------------------------------
            */
    
            $response = $this->firebase->sendTopicNotification(
    
                $topic,
    
                $title,
    
                $body,
    
                $payload
    
            );
    
            /*
            |--------------------------------------------------------------------------
            | Update Success
            |--------------------------------------------------------------------------
            */
    
            $this->updateNotificationLog(
    
                $logId,
    
                1,
    
                $response
    
            );
    
            return [
    
                'status' => true,
    
                'response' => $response
    
            ];
    
        } catch (\Exception $e) {
    
            Log::error('Firebase Topic Notification', [
    
                'topic' => $topic,
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
    
            ]);
    
            if (!empty($logId)) {
    
                $this->updateNotificationLog(
    
                    $logId,
    
                    2,
    
                    [
    
                        'error' => $e->getMessage()
    
                    ]
    
                );
    
            }
    
            return [
    
                'status' => false,
    
                'message' => $e->getMessage()
    
            ];
    
        }
    
    }
    
    public function sendToUser(
        $userId,
        $title,
        $body,
        $type,
        $screen,
        array $data = []
    )
    {
    
        try {
    
            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */
    
            $user = DB::table('customer_register')
    
                ->where('id', $userId)
    
                ->select(
    
                    'id',
    
                    'fcm_token'
    
                )
    
                ->first();
    
            if (!$user) {
    
                return [
    
                    'status' => false,
    
                    'message' => 'User not found.'
    
                ];
    
            }
    
            if (empty($user->fcm_token)) {
    
                return [
    
                    'status' => false,
    
                    'message' => 'FCM token not available.'
    
                ];
    
            }
    
            /*
            |--------------------------------------------------------------------------
            | Payload
            |--------------------------------------------------------------------------
            */
    
            $payload = array_merge([
    
                'type' => $type,
    
                'screen' => $screen,
    
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
    
            ], $data);
    
            /*
            |--------------------------------------------------------------------------
            | Save Notification
            |--------------------------------------------------------------------------
            */
    
            $logId = $this->saveNotificationLog(
    
                $title,
    
                $body,
    
                $userId,
    
                'user',
    
                [
    
                    'user_id' => $userId,
    
                    'payload' => $payload
    
                ],
    
                null,
    
                0
    
            );
    
            /*
            |--------------------------------------------------------------------------
            | Firebase
            |--------------------------------------------------------------------------
            */
    
            $response = $this->firebase->sendTokenNotification(
    
                $user->fcm_token,
    
                $title,
    
                $body,
    
                $payload
    
            );
    
            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */
    
            $this->updateNotificationLog(
    
                $logId,
    
                1,
    
                $response
    
            );
    
            return [
    
                'status' => true,
    
                'response' => $response
    
            ];
    
        } catch (\Exception $e) {
    
            Log::error('Firebase User Notification', [
    
                'user_id' => $userId,
    
                'message' => $e->getMessage(),
    
                'line' => $e->getLine()
    
            ]);
    
            if (!empty($logId)) {
    
                $this->updateNotificationLog(
    
                    $logId,
    
                    2,
    
                    [
    
                        'error' => $e->getMessage()
    
                    ]
    
                );
    
            }
    
            return [
    
                'status' => false,
    
                'message' => $e->getMessage()
    
            ];
    
        }
    
    }
    
    public function sendToUsers(
        array $userIds,
        $title,
        $body,
        $type,
        $screen,
        array $data = []
    )
    {
    
        if (empty($userIds)) {
    
            return [
    
                'status' => false,
    
                'message' => 'No users found.'
    
            ];
    
        }
    
        $success = 0;
    
        $failed = 0;
    
        $responses = [];
    
        foreach (array_unique($userIds) as $userId) {
    
            $result = $this->sendToUser(
    
                $userId,
    
                $title,
    
                $body,
    
                $type,
    
                $screen,
    
                $data
    
            );
    
            if ($result['status']) {
    
                $success++;
    
            } else {
    
                $failed++;
    
            }
    
            $responses[] = [
    
                'user_id' => $userId,
    
                'status' => $result['status'],
    
                'message' => $result['message'] ?? null
    
            ];
    
        }
    
        return [
    
            'status' => true,
    
            'success' => $success,
    
            'failed' => $failed,
    
            'responses' => $responses
    
        ];
    
    }
    
    public function sendGroupInvitation($userId, $groupId, $groupName, $invitedBy)
    {
    
        return $this->sendToUser(
    
            $userId,
    
            'Group Invitation',
    
            "{$invitedBy} invited you to join {$groupName}.",
    
            self::TYPE_GROUP_INVITE,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendGroupJoined($userIds, $groupId, $groupName, $memberName)
    {
    
        return $this->sendToUsers(
    
            $userIds,
    
            'New Member Joined',
    
            "{$memberName} joined {$groupName}.",
    
            self::TYPE_GROUP_JOIN,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendMemberLeft($userIds, $groupId, $groupName, $memberName)
    {
    
        return $this->sendToUsers(
    
            $userIds,
    
            'Member Left',
    
            "{$memberName} left {$groupName}.",
    
            self::TYPE_GROUP_LEFT,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendMemberRemoved($userId, $groupId, $groupName)
    {
    
        return $this->sendToUser(
    
            $userId,
    
            'Removed From Group',
    
            "You have been removed from {$groupName}.",
    
            self::TYPE_GROUP_REMOVE,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendMakeAdmin($userId, $groupId, $groupName)
    {
    
        return $this->sendToUser(
    
            $userId,
    
            'Promoted To Admin',
    
            "You are now an admin of {$groupName}.",
    
            self::TYPE_GROUP_ADMIN,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendRemoveAdmin($userId, $groupId, $groupName)
    {
    
        return $this->sendToUser(
    
            $userId,
    
            'Admin Removed',
    
            "Your admin access has been removed from {$groupName}.",
    
            self::TYPE_GROUP_ADMIN,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendOwnershipTransferred($userId, $groupId, $groupName)
    {
    
        return $this->sendToUser(
    
            $userId,
    
            'Group Ownership',
    
            "You are now the owner of {$groupName}.",
    
            self::TYPE_TRANSFER_OWNER,
    
            self::SCREEN_GROUP,
    
            [
    
                'group_id' => (string)$groupId
    
            ]
    
        );
    
    }
    
    public function sendSocietyJobShared(
        $topic,
        $groupId,
        $jobId
    )
    {
    
        return $this->sendToTopic(
    
            $topic,
    
            '🚖 New Ride Request',
    
            'One of your society members posted a new ride request.',
    
            self::TYPE_SOCIETY_JOB,
    
            self::SCREEN_SOCIETY_JOB,
    
            [
    
                'group_id' => (string)$groupId,
    
                'job_id' => (string)$jobId
    
            ]
    
        );
    
    }
    
}