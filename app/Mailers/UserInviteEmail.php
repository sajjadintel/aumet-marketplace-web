<?php

namespace App\Mailers;

class UserInviteEmail
{
    protected $subject = 'New User Invite';
    protected $viewPath = 'email/userInvitation.php';
    /** @var \EmailHandler */
    protected $handler;
    protected $type = 'newUserInvite';
    /** @var \Base */
    protected $f3;

    public function __construct($emails, $db, $object)
    {
        $this->handler = new \EmailHandler($db);
        if (is_array($emails)) {
            foreach ($emails as $email) {
                $this->handler->appendToAddress($email, $email);
            }
        } else if (is_string($emails)) {
            $this->handler->appendToAddress($emails, $emails);
        }

        $entity = (new \EntityUserProfileView)->findone(['entityId = ?', $object->entityId]);
        $this->f3 = \Base::instance();
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'User Invitation');
        $this->f3->set('emailType', 'userInvitation');
        $this->f3->set('token', $object->token);
        $arrFields = [
            "Email" => $object->email,
            "Distributor Name" => $entity->entityName_en,
            "Country" => $entity->entityCountryName_en,
            "City" => $entity->entityBranchCityName_en,
            "Address" => $entity->entityBranchAddress_en,
        ];
        $this->f3->set('arrFields', $arrFields);
    }

    public static function send($emails, $db, $object)
    {
        $instance = new self($emails, $db, $object);
        $html = \View::instance()->render($instance->viewPath);
        return $instance->handler->sendEmail(
            $instance->type,
            $instance->subject,
            $html
        );
    }
}