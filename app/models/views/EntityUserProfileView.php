<?php


class EntityUserProfileView extends BaseModel
{
    protected $table_name = 'vwEntityUserProfile';

    public static function getEntityIdFromUser($userId)
    {
        $object = new self;
        $object->findone(['userId = ?', $userId]);

        return $object ? $object->entityId : null;
    }
}