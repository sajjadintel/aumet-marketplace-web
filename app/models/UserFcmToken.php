<?php

class UserFcmToken extends BaseModel
{
    protected $table_name = 'user_fcm_tokens';
    const TOKEN_PLATFORM_WEB = 'web';
    const TOKEN_PLATFORM_MOBILE = 'mobile';

    /**
     * @param int $userId
     * @return UserFcmToken
     */
    public static function getWebTokenForUser($userId)
    {
        return (new self)->find(['platform' => self::TOKEN_PLATFORM_WEB, 'user_id' => $userId])[0];
    }

    /**
     * @param int $userId
     * @param string $token
     * @return array|mixed
     */
    public static function setWebTokenForUser($userId, $token)
    {
        $object = (new self)->find(['user_id' => $userId, 'platform' => self::TOKEN_PLATFORM_WEB])[0];
        if (!empty($object)) {
            $object->fcm_token = $token;
            return $object->save();
        }
        $object = new self;
        $object->user_id = $userId;
        $object->fcm_token = $token;
        $object->platform = self::TOKEN_PLATFORM_WEB;
        return $object->save();
    }
}