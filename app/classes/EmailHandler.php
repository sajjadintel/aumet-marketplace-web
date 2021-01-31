<?php

class EmailHandler
{
    protected $sendGrid;
    private $arrBCC = [];
    private $arrTos = [];
    protected $exception;
    private $f3;
    private $db;
    
    function __construct($db)
    {
        $this->f3 = \Base::instance();
        $this->db = $db;
    }
    
    public function setBCC($arrBCC)
    {
        $this->arrBCC = $arrBCC;
    }
    
    public function clearBCC()
    {
        $this->arrBCC = [];
    }
    
    public function appendToBcc($email, $name)
    {
        $this->arrBCC[$email] = $name;
    }
    
    public function appendToAddress($email, $name)
    {
        $this->arrTos[$email] = $name;
    }
    
    public function resetTos()
    {
        $this->arrTos = [];
    }
    
    public function setTos($email, $name)
    {
        $this->arrTos = [$email => $name];
    }
    
    public function getException()
    {
        return $this->exception;
    }

    public function sendEmail($emailType, $subject, $html)
    {
        if (count($this->arrTos) <= 0) {
            return -1;
        }
        try {
            $email = new \SendGrid\Mail\Mail();
            $email->setFrom(getenv('MAIN_EMAIL_FROM'), getenv('MAIN_EMAIL_FROM_NAME'));
            $email->setSubject($subject);
            $email->addTos($this->arrTos);
            if (count($this->arrBCC)) {
                $email->addBccs($this->arrBCC);
            }
            $email->addContent(
                "text/html",
                $html
            );
            $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
            $response = $sendgrid->send($email);
            $statusCode = $response->statusCode();

            $emailTos = implode(", ", array_keys($this->arrTos));
            $this->logEmailTransaction($emailType, $emailTos, $subject, $html, $statusCode);
            
            return $statusCode;
        } catch (Exception $e) {
            $this->exception = $e;
            return -1;
        }
    }
    
    public function logEmailTransaction($emailType, $emailTos, $emailSubject, $emailContent, $emailStatusCode)
    {
        $dbLog = new BaseModel($this->db, "emailLog");
        $dbLog->type = $emailType;
        $dbLog->tos = $emailTos;
        $dbLog->subject = $emailSubject;
        $dbLog->content = $emailContent;
        $dbLog->statusCode = $emailStatusCode;
        $dbLog->add();
    }
}
