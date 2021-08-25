<?php
namespace System\HttpResponse;

class HttpResponse 
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
     * @param integer $status 200, 418, 419, 420
     * 
     * 200 - Success
     * 
     * 418 - Infor
     * 
     * 419 - Failure
     * 
     * 420 - Fatal error
     * 
     * @return void
     */
    public function HttpResponse(string $response, int $status = 200) {
        switch($status){
            case 418:
                echo $this->info($response);
                break;
            case 419:
                echo $this->failure($response);
                break;
            case 420:
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
    public function JsonResponse(array $response, bool $echo = true) {
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
     * @param string $url
     * The url whether to redirect
     * @return void
     */
    public function redirect($url) {
        header("location:".$url);
        exit();
    }


    /**
     * Return a well formatted and bootstrapped HttpResponse
     *
     * @param string $response The response message
     * @param integer $status 200, 418, 419, 420
     * 
     * 200 - Success
     * 
     * 418 - Infor
     * 
     * 419 - Failure
     * 
     * 420 - Fatal error
     * 
     * @return string Use this response later in your code
     */
    public function RowHttpResponse(string $response, int $status = 200) {
        switch($status){
            case 418:
                $response = $this->info($response);
                break;
            case 419:
                $response = $this->failure($response);
                break;
            case 420:
                $response = $this->danger($response);
                break;
            default:
            $response = $this->success($response);
        }

        return $response;
    }
}
