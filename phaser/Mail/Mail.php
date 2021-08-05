<?php
namespace PHASER\Mail;
use FFI\Exception;

/**
 * Send Mail using mail()
 */
class Mail
{
    public $To;
    public $From;
    public $Subject;
    public $Body;
    public $ReplyTo;
    public $Cc;
    public $Bcc;



    public function __construct()
    {
        
    }

    public function send(bool $is_html = false) {

        // To send HTML mail, the Content-type header must be set
        if ($is_html) {
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=utf-8';
        }else {
            $headers[] = 'Content-type: text/plain; charset=utf-8';
        }

        // Additional headers
        $headers[] = 'To: '.$this->To;
        $headers[] = 'From: '.$this->Form;
        !empty($this->Cc) ? $headers[] = 'Cc: '.$this->Cc : null;
        !empty($this->Bcc) ? $headers[] = 'Bcc: '.$this->Bcc: null;
        try {
            return mail($this->To, $this->Subject, $this->Body, implode("\r\n", $headers));
        } catch(Exception $e){
            // return error_get_last()['message'];
        }
    } 
}