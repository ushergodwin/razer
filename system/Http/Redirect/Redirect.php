<?php
namespace System\Http\Redirect;

class Redirect
{
    public $url;

    public $status = 200;
    
    private function httpReferer()
    {
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if(!empty($referer))
        {
            $referer = str_replace(url(), '', $referer);
        }
        return $referer;
    }


    /**
     * Send a Redirect Http
     *
     * @param string $url
     * @return void
     */
    protected function to($url)
    {
        $url = str_replace('.', '/', $url);
        $url = url($url);
        header('location: ' . $url);
        exit;
    }
    /**
     * Return to the previous request URI
     *
     * @return \System\Http\Redirect\Redirect
     */
    public function back()
    {
        $this->url = $this->httpReferer();
        return $this;
    }


    /**
     * Redirect to the previous Request URI with a message
     * @param string $key
     * @param string $message
     * @return \System\Http\Redirect\Redirect
     */ 
    public function with(string $key, string $message)
    {
        session(['responseMessage' => array_to_object([$key => $message])]);
        return $this;
    }


    /**
     * Redirect back with input data previously submited
     * @return \System\Http\Redirect\Redirect
     */
    public function withInput()
    {
        session(["responseData" => $_REQUEST]);
        return $this;
    }

    public function __destruct()
    {
        $this->to($this->url);
    }
}