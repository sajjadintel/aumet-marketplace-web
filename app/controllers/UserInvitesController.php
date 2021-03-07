<?php

class UserInvitesController extends Controller
{
    public function index()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        if (!$entityId) {
            return;
        }

        $invite = new UserInvite;

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->data = $invite->find(['entityId = ?', $entityId]);
        echo $this->jsonResponse(Constants::STATUS_SUCCESS, $invite->find(['entityId = ?', $entityId]));
    }

    public function create()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        if (!$entityId) {
            return;
        }

        $invite = new UserInvite;
        $invite->entityId = $entityId;
        $invite->email = $this->f3->get('POST.email');
        $invite->token = bin2hex(random_bytes(16));
        $invite->createdAt = (new DateTime)->format('Y-m-d H:i:s');
        $invite->save();
    }

    public function process()
    {
        $invite = new UserInvite;
        $invite = $invite->findone(['email = ? AND token = ? AND used = ?', $this->f3->get('POST.email'), $this->f3->get('POST.token'), false]);
        if (!$invite) {
            return;
        }

        $invite->used = true;
        $invite->save();
    }
}