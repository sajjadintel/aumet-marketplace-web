<?php


class User extends BaseModel
{
    use Validate;
    protected $table_name = 'user';

    public function getRules()
    {
        return [
            'name' => 'required',
            'email' => 'required|unique,email,user',
            'mobile' => 'required|unique,mobile,user',
            'password' => 'required'
        ];
    }

    public function create($data, $isDistributor = false, $invited = false)
    {
        $validation = $this->check($data);
        if ($validation !== true) {
            return $validation;
        }

        if ($data['uid'] != NULL && trim($data['uid']) != '') {
            $this->uid = $data['uid'];
        }
        $this->email = $data['email'];
        $this->password = password_hash($data['password'], PASSWORD_DEFAULT);
        $this->statusId = $invited ? Constants::USER_STATUS_ACCOUNT_ACTIVE : Constants::USER_STATUS_WAITING_VERIFICATION;
        $this->fullname = $data['name'];
        $this->mobile = $data['mobile'];
        $this->roleId = $isDistributor ? Constants::USER_ROLE_DISTRIBUTOR_SYSTEM_ADMINISTRATOR : Constants::USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR;
        $this->language = "en";
        $this->save();
        return $this;
    }
}