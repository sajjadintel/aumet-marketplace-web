<?php

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

class FcmHandler
{
    private $title;
    private $body;
    private $type;
    private $options;
    /**
     * @var \DB\SQL\Mapper[] $users
     */
    private $users;

    const NOTIFICATION_TYPE_NEW_ORDER = 1;

    public function __construct(string $title, string $body, string $type, $users, $options = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->users = $users;
        $this->type = $type;
        $this->options = $options ?? [];
    }

    /**
     * Append a token to the users array
     * @param \DB\SQL\Mapper $user
     */
    public function appendToken(string $user)
    {
        $this->users[] = $user;
    }

    public function send()
    {
        $messaging = (new Factory)->createMessaging();
        $cloudMessageArray = [
            'notification' => [
                'title' => $this->title,
                'body' => $this->body
            ]
        ];

        $reports = [];

        $message = CloudMessage::fromArray($cloudMessageArray);
        if (!empty($this->options)) {
            $message = $message->withWebPushConfig($this->options);
        }
        for ($i = 0; $i <= count($this->users) / 500; $i++) {
            $chunk = array_slice($this->users, $i * 500, 500);
            $tokens = (new UserFcmToken)->select('fcm_token', [
                'user_id IN (?)', implode(',', array_column($chunk, 'id'))
            ]);
            $tokens = array_column($tokens, 'fcm_token');
            if (empty($tokens)) {
                continue;
            }

            $reports[] = $messaging->sendMulticast($message, $tokens);
            $this->storeNotification($chunk);
        }

        return $reports;
    }

    public function storeNotification($users)
    {
        foreach ($users as $user) {
            $notification = new Notification;
            $notification->user_id = $user->id;
            $notification->title = $this->title;
            $notification->body = $this->body;
            $notification->type = $this->type;
            $notification->save();
        }
    }
}