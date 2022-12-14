<?php

class UserInvite extends BaseModel
{
    use Validate;

    protected $table_name = 'userInvites';
    public $hasErrors = false;
    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUSES = [
        self::STATUS_PENDING => 'pending',
        self::STATUS_CONFIRMED => 'confirmed',
    ];

    public function getRules()
    {
        return [
            'id' => 'numeric',
            'email' => 'required|email|unique,email,user|unique,email,userInvites',
            'token' => 'required|unique,token,userInvites',
        ];
    }

    public function statusIsPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Validates and inserts a new record into user invites, if validation fails the parameter hasErrors
     * will be true, and errors will be filled with the validation messages
     *
     * @param $email string
     * @param $entityId int
     * @param $userId int
     * @return $this
     * @throws Exception
     */
    public function create($email, $entityId, $userId)
    {
        $data = [
            'createdBy' => $userId,
            'entityId' => $entityId,
            'status' => self::STATUS_PENDING,
            'email' => $email,
            'token' => bin2hex(random_bytes(16)),
            'createdAt' => (new DateTime)->format('Y-m-d H:i:s'),
        ];

        if ($this->check($data) !== true) {
            $this->hasErrors = true;
            return $this;
        }

        foreach ($data as $key => $datum) {
            $this->$key = $datum;
        }

        $this->save();
        App\Mailers\UserInviteEmail::send($email, $this->db, $this);
        return $this;
    }

    public function process()
    {
        $this->status = self::STATUS_CONFIRMED;
        $this->save();

        return $this;
    }

    public function destroy($inviteId, $entityId)
    {
        $invite = $this->findone(['id = ?', $inviteId]);
        if ($invite === false) {
            $this->hasErrors = true;
            $this->errors[] = Base::instance()->get('user_invite_not_found');
            return $this;
        }

        if ($invite->entityId !== $entityId) {
            $this->hasErrors = true;
            $this->errors[] = Base::instance()->get('unauthorized_to_delete_user_invite');
            return $this;
        }

        if (!$invite->statusIsPending()) {
            $this->hasErrors = true;
            $this->errors[] = Base::instance()->get('user_invite_is_processed');
            return $this;
        }

        return $invite->delete();
    }

    public static function findByToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $model = new self;
        return $model->findone(['token = ?', $token]);
    }

    public static function findByTokenAndStatus($token, $status)
    {
        $model = new self;
        if (empty($token)) {
            $model->hasErrors = true;
            $model->errors[] = str_replace('{0}', 'token', $model->getDefaultErrorMessages()['required']);
            return $model;
        }
        
        return $model->findone(['token = ? AND status = ?', $token, $status]);
    }

    public function findWhereWith($query, $order, $limit, $offset)
    {
        $data = $this->findWhere($query, $order, $limit, $offset);
        $ids = array_filter(array_column($data, 'createdBy'));
        if (empty($ids)) {
            return $data;
        }
        
        $users = (new User)->findWhere('id IN (' . implode(',', $ids) . ')');
        foreach ($data as &$datum) {
            foreach ($users as $index => $user) {
                if ($user['id'] === $datum['createdBy']) {
                    $datum['createdBy'] = "{$user['id']} - {$user['fullname']}";
                    unset($users[$index]);
                }
            }

            $datum['status'] = self::STATUSES[$datum['status'] ?? 0];
        }

        return $data;
    }
}