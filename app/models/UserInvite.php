<?php

class UserInvite extends BaseModel
{
    use Validate;

    protected $table_name = 'userInvites';

    public function getRules()
    {
        return [
            'id' => 'numeric',
            'email' => 'required|email|unique,email,user|unique,email,userInvites',
        ];
    }

    public function destroy($inviteId, $entityId)
    {
        $invite = $this->findone(['id = ? AND entityId = ?', $inviteId, $entityId]);
        if ($invite === false) {
            return false;
        }

        return $invite->delete();
    }
}