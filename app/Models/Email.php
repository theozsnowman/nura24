<?php

namespace App\Models;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core;
use DB;

class Email extends Model
{
    /**
    * Send SMTP email 
    */
    public function send_email (array $args, array $attachments = null) {

        $this->config = Core::config();     

        $to_email = $args['to_email'];
        $subject = $args['subject'] ?? null;        
        $body = $args['body'] ?? null;                

        $mail = new PHPMailer;
            
        $mail->IsSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->config->smtp_server;                 // Specify main and backup server
        $mail->Port = $this->config->smtp_port;                                    // Set the SMTP port
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->config->smtp_user;                // SMTP username
        $mail->Password = $this->config->smtp_password;                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->setFrom($this->config->site_email, $this->config->site_email_name);
        $mail->addReplyTo($this->config->site_email, $this->config->site_email_name);
        $mail->AddAddress($to_email);  
                
        $mail->IsHTML(true);         
        
        // Attachments
        if(!empty($attachments)) {
            foreach($attachments as $attachment) {
                $mail->addAttachment($attachment); 
            }
        }

        $mail->Subject = $subject;
        $mail->Body = $body;

        if(!$mail->Send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            exit;
            }   

        return;    
    }

}

