<?php

class UserInviteEmail
{
    protected $subject = 'New User Invite';
    protected $viewPath = 'emails/new_user_invite.php';
    protected $handler;
    protected $type = 'New User Invite';

    public function __construct($emails, $db)
    {
        $this->handler = new EmailHandler($db);
        if (is_array($emails)) {
            foreach ($emails as $email) {
                $this->handler->appendToAddress($email, $email);
            }
        } else if (is_string($emails)) {
            $this->handler->appendToAddress($emails, $emails);
        }
    }

    public static function send($emails, $db)
    {
        $instance = new self($emails, $db);
        $html = View::instance()->render($instance->viewPath);
        return $instance->handler->sendEmail(
            $instance->type,
            $instance->subject,
            $html
        );
    }
}