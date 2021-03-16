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
        $data = $invite->findWhereWith($query, $order, $datatable->limit, $datatable->offset);
        $resultCount = $invite->count($query);

        $response = [
            "draw" => intval($datatable->draw),
            "recordsTotal" => $resultCount,
            "recordsFiltered" => $resultCount,
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
        $invite = $invite->create($this->f3->get('POST.email'), $entityId, $this->objUser->id);
        if ($invite->hasErrors) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = implode("\n", array_values($invite->errors));
            echo $this->webResponse->jsonResponse();
            return;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
        $this->webResponse->message = $this->f3->get('vModule_productAdded');
        echo $this->webResponse->jsonResponse();
    }

    public function destroy()
    {
        $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
        $invite = new UserInvite;
        $invite = $invite->destroy($this->f3->get('PARAMS.id'), $entityId);
        if($invite instanceof UserInvite && $invite->hasErrors) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = implode("\n", $invite->errors);
            echo $this->webResponse->jsonResponse();
            return;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
        $this->webResponse->message = 'Deleted successfully';
        echo $this->webResponse->jsonResponse();
    }
}