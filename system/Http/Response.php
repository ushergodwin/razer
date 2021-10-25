<?php
namespace System\Http;
class Response 
{

    private $f;
    private $b;
    private $times;
    private $btn_class;

    public function __construct()
    {
        $version = (int) env("FONT_AWESOME_VERSION");
        $boostrap = (int) env("BOOTSTRAP_VERSION");
        $this->f = $version >= 5 ? "fas" : "fa";
        $this->b = $boostrap > 4 ? "data-bs-dismiss" : "data-dismiss";
        $this->times = $boostrap > 4 ? "" : "&times;";
        $this->btn_class = $boostrap > 4 ? "btn-close" : "close";
    }

    /**
     * Return a well formatted and bootstrapped HttpResponse
     *
     * @param string $response The response message
     * @param integer $status 200, 418, 419, 500
     * @param bool $return Return a string response instead of echo
     * 
     * 200 - Success
     * 
     * 418 - Infor
     * 
     * 419 - Failure
     * 
     * 500 - Server Error
     * 
     * @return void
     */
    public function http(string $response, int $status = 200, bool $return = false)
     {
        if($return)
        {
            return $this->rowHttp($response, $status);
        }

        switch($status){
            case 418:
                echo $this->info($response);
                break;
            case 419:
                echo $this->failure($response);
                break;
            case 500:
                echo $this->danger($response);
                break;
            default:
            echo $this->success($response);
        }
    }

    /**
     * A convenient way of parsing a json response
     *
     * @param array $response response
     * @return void
     */
    public function json(array $response, bool $echo = true) {
        if (!$echo)
            return json_encode($response);
        echo json_encode($response);
    }

    private function success($message) {
        return "<div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong><i class='{$this->f} fa-check-circle text-success'></i></strong> {$message}
        <button type='button' class='{$this->btn_class}' {$this->b}='alert'>{$this->times}</button>
        </div>";
    }

    private function failure($message) {
        return "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
        <strong><i class='{$this->f} fa-exclamation-triangle text-warning'></i></strong> {$message}
        <button type='button' class='{$this->btn_class}' {$this->b}='alert'>{$this->times}</button>
        </div>";
    }

    private function info($message) {
        return "<div class='alert alert-info alert-dismissible fade show' role='alert'>
        <strong><i class='fas fa-info-circle text-info'></i></strong> {$message}
        <button type='button' class='{$this->btn_class}' {$this->b}='alert'>{$this->times}</button>
        </div>";
    }

    private function danger($message) {
        return "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
        <strong><i class='{$this->f} fa-exclamation-triangle text-danger'></i></strong> {$message}
        <button type='button' class='{$this->btn_class}' {$this->b}='alert'>{$this->times}</button>
        </div>";
    }


    /**
     * Return a well formatted and bootstrapped HttpResponse
     *
     * @param string $response The response message
     * @param integer $status 200, 418, 419, 500
     * 
     * 200 - Success
     * 
     * 418 - Infor
     * 
     * 419 - Failure
     * 
     * 500 - Fatal error
     * 
     * @return string Use this response later in your code
     */
    private function rowHttp(string $response, int $status = 200) {
        switch($status){
            case 418:
                $response = $this->info($response);
                break;
            case 419:
                $response = $this->failure($response);
                break;
            case 500:
                $response = $this->danger($response);
                break;
            default:
            $response = $this->success($response);
        }

        return $response;
    }

    public function BadRequest() {
        header("HTTP/1.0 403 Bad Request");
        exit("<div align='center'><a href='/'><img src='".url('403.jpg')."'/> </div></a> ");
    }

    /**
     * Get the Response message for redirected responses
     *@param bool $plain Indiate that you want a plain text message
     *
     * The response message is by default formated into HTML
     * @return string
     */
    public function message(bool $plain = false)
    {
        $message = session('responseMessage');
        if(empty(session('plainMessage')))
        {
            $message = '';
        }
        if(!isset($_SESSION['responseData']))
        {
            unset($_SESSION['responseMessage']);
            unset($_SESSION['plainMessage']);
            $message = '';
        }
        if($plain)
        {
            $message = session('plainMessage');
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
