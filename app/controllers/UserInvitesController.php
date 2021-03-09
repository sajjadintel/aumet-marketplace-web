<?php

class UserInvitesController extends Controller
{
    public function index()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        if (!$entityId) {
            return;
        }

        $datatable = new Datatable($_POST);

        $invite = new UserInvite;
        $order = "$datatable->sortBy $datatable->sortByOrder";
        $query = "entityId = {$entityId}";
        $data = $invite->findWhere($query, $order, $datatable->limit, $datatable->offset);

        $response = [
            "draw" => intval($datatable->draw),
            "recordsTotal" => $invite->count($query),
            "recordsFiltered" => $invite->count($query),
            "data" => $data,
            "query" => $query
        ];
        $this->jsonResponseAPI($response);
    }

    public function create()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        if (!$entityId) {
            return;
        }

        $invite = new UserInvite;
        $validation = $invite->check($this->f3->get('POST'));
        if (is_array($validation)) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = implode("\n", array_values($validation));
            echo $this->webResponse->jsonResponse();
            return;
        }

        $invite->entityId = $entityId;
        $invite->email = $this->f3->get('POST.email');
        $invite->token = bin2hex(random_bytes(16));
        $invite->createdAt = (new DateTime)->format('Y-m-d H:i:s');
        $invite->save();
        UserInviteEmail::send($this->f3->get('POST.email'), $this->db);

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
        $this->webResponse->message = $this->f3->get('vModule_productAdded');
        echo $this->webResponse->jsonResponse();
    }

    public function destroy()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        $invite = new UserInvite;
        if(!$invite->destroy($this->f3->get('PARAMS.id'), $entityId)) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = 'You cant delete this invite';
            echo $this->webResponse->jsonResponse();
            return;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
        $this->webResponse->message = 'Deleted successfully';
        echo $this->webResponse->jsonResponse();
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