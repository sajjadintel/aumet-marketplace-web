<?php

/**
 * Description of WebResponse
 *
 * @author Alaa
 */
class WebResponse
{
    public $errorCode;
    public $message;
    public $data;
    public $title;

    public function __construct()
    {
        $this->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB; // 0 means no error
        $this->message = "";
        $this->data = null;
        $this->title = null;
    }

    public function jsonResponse()
    {

        // clear the old headers
        header_remove();
        // set the actual code
        http_response_code(200);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');
        // ok, validation error, or failure
        header('Status: 200 OK');
        // return the encoded json
        return json_encode($this);
    }

    public function jsonResponseV2($errorCode = 0, $title = null, $message = '', $data = null)
    {

        $this->errorCode = $errorCode; // 0 means no error
        $this->message = $message;
        $this->data = $data;
        $this->title = $title;
        return $this->jsonResponse();
    }
}
