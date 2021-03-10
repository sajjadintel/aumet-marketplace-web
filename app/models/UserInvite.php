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
            'token' => 'required|unique,token,userInvites',
        ];
    }

    public function create($email, $entityId)
    {
        $data = [
            'entityId' => $entityId,
            'email' => $email,
            'token' => bin2hex(random_bytes(16)),
            'createdAt' => (new DateTime)->format('Y-m-d H:i:s'),
        ];

        $result = $this->check($data);
        if ($result !== true) {
            return $result;
        }

        foreach ($data as $key => $datum) {
            $this->$key = $datum;
        }

        $this->save();
        App\Mailers\UserInviteEmail::send($email, $this->db, $this);
        return $this;
    }

    public function destroy($inviteId, $entityId)
    {
        $invite = $this->findone(['id = ? AND entityId = ?', $inviteId, $entityId]);
        if ($invite === false) {
            return false;
        }

        return $invite->delete();
    }

    public static function findByToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $model = new static();
        return $model->findone(['token = ? AND used = ?', $token, false]);
    }
}