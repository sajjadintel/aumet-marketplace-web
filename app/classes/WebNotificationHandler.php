<?php

use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

class WebNotificationHandler
{
    /**
     * @var string[]
     */
    private $tokens;
    private $title;
    private $body;

    public function __construct(string $title = null, string $body = null, $tokens = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->tokens = $tokens;
    }

    /**
     * Append a token to the tokens array
     * @param string $token
     */
    public function appendToken(string $token)
    {
        $this->tokens[] = $token;
    }

    public function send()
    {
        $messaging = (new Factory())->createMessaging();
        $message = CloudMessage::fromArray([
            'title' => $this->title,
            'body'  => $this->body,
        ]);

        for ($i = 0; $i <= count($this->tokens) / 500; $i++) {
            $messaging->sendMulticast(
                $message,
                array_slice(
                    $this->tokens,
                    $i * 500,
                    500
                )
            );
        }
    }
}