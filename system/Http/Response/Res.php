<?php

namespace System\Http\Response;

class Res
{
    protected function status(int $code)
    {
        $status = array(
            200 => '200 OK',
            201 => 'Created',
            202 => 'Accepted',
            302 => 'Found',
            400 => '400 Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Not Found',
            404 => 'Not Found',
            408 => 'Request Timeout',
            422 => 'Unprocessable Entity',
            500 => '500 Internal Server Error',
            502 => 'Bad Getway'
            );
        return $status[$code];
    }


    public function send(int $status, string $message)
    {
        header_remove();
        // set the actual code
        http_response_code($status);
        header("HTTP/1.0 " . $this->status($status));
        echo $message;
    }

    public function json(int $status, $message = null)
    {
           // clear the old headers
        header_remove();
        // set the actual code
        http_response_code($status);
        // set the header to make sure cache is forced
        header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        // treat this as json
        header('Content-Type: application/json');
 
        // ok, validation error, or failure
        header('Status: '.$this->status($status));
        // return the encoded json
        echo json_encode(array(
            'status' => $status, // success or not?
            'message' => $message
        ));
    }

    public function badRequest() {
        header("HTTP/1.0 400 Bad Request");
        exit("<div align='center'><a href='/'><img src='".url('400.jpg')."'/> </div></a> ");
    }

    /**
     * Get the Response message for redirected responses
     *@param bool $plain Indiate that you want a plain text message
     *
     * The response message is by default formated into HTML
     * @return string
     */
    public function message()
    {
        $message = session('responseMessage');
        if(!isset($_SESSION['responseData']))
        {
            unset($_SESSION['responseMessage']);
            $message = '';
        }
        return $message;
    }


    /**
     * Get Validation errors
     *
     * @return array
     */
    public function errors()
    {
        $errors = session('errors');
        if(!isset($_SESSION['responseData']))
        {
            $errors = [];
        }
        return $errors;
    }
}