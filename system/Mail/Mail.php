<?php
namespace System\Mail;
use Exception;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

/**
 * Send Mail
 */
class Mail extends PHPMailer
{
    public function __construct()
    {
        parent::__construct(true);
        try{
            $this->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->isSMTP();
            $this->Host = env('SMTP_HOST');
            $this->SMTPAuth = true;
            $this->Username = env('SMTP_USERNAME');
            $this->Password = env('SMTP_PASSWORD');
            $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->Port = (int)env('SMTP_PORT');
            $this->setFrom(env('DEFAULT_SENDER'), env('DEFAULT_SENDER_NAME'));
        } catch(Exception $e){
            echo "Message could not be sent. Mailer Error: {$this->ErrorInfo}";
        }
    }

    public function Recipients(array $recipients)
    {
        if(empty($recipients))
        {
            throw new PHPMailerException('The Recipients must have at least one person');
        }

        foreach($recipients as $key => $value)
        {
            $this->addAddress($key, $value);
        }
        
    }

    public function send()
    {
        try{
            return parent::send();
        } catch(Exception $e){
            $message = "Message could not be sent. Mailer Error: {$this->ErrorInfo}";
            return response()->json([
                'status' => 419,
                'message' => response()->http($message, 418, true)
            ]);
        }
    }
}