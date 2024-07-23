<?php

namespace Drupal\mz_email;

/**
 * Class DefaultService.
 */
//require composer require phpmailer/phpmailer
class DefaultService {

  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {

  }
  public function sendMail($sentTo , $subject , $body ){
    $config = \Drupal::config('mz_email.config');
    if($config->get('is_not_smtp')){
      $headers = 'From: sender@example.com' . "\r\n" .
                 'Reply-To: sender@example.com' . "\r\n" .
                 'X-Mailer: PHP/' . phpversion();
  
      if (mail($sentTo, $subject, $body, $headers)) {
        \Drupal::messenger()->addMessage(t('Email simple sent successfully.'));
      } else {
        \Drupal::messenger()->addMessage(t('There was a problem sending your email simple.'), 'error');
        return false;
      }
      return true ;
    }
    if(!$this->verificationMail($sentTo)){
        \Drupal::logger('mz_email')->error("E-mail is not valid ".$sentTo);
        return false ;
    }
    if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
       
        $mail = new \PHPMailer\PHPMailer\PHPMailer();       
        // Set mailer to use SMTP
        $mail->isSMTP();
        
        // SMTP settings for Gmail
      
       // $mail->Host = 'smtp.gmail.com';
        //$mail->Username = 'boutamiandrilala@gmail.com';
        //$mail->Password = 'Bota@#009856';
        
        $mail->Host = $config->get('host');
        $mail->Username = $config->get('username'); // Your Gmail address
        $mail->Password = $config->get('password'); // Your Gmail password

        $mail->SMTPAuth = true;
        $mail->SMTPSecure = $config->get('secure');
        $mail->Port = $config->get('port');
        
        // Sender and recipient details
        $mail->setFrom($config->get('sender'), $config->get('sender_label'));
        $mail->addAddress($config->get('sender'), $config->get('sender_label'));
        if(is_string($sentTo)){
            $mail->addAddress($sentTo, 'Recipient');
        }
        if(is_array($sentTo)){
            foreach($sentTo as $to){
                $mail->addAddress($to, 'Recipient');
            }
        }
        // Email subject and body
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        // Send the email
        if(!$mail->send()) {
            $message = 'Message could not be sent.';
            \Drupal::logger('mz_email')->error( $message);
            $message =  'Mailer Error: ' . $mail->ErrorInfo;
            \Drupal::logger('mz_email')->error( $message);
            return false ;
        } else {
            \Drupal::logger('mz_email')->info('Message has been sent');
            \Drupal::messenger()->addMessage('Email Message has been sent');  
            return true ;
        } 

    } else {
        // PHPMailer library is not available
        // Handle the situation accordingly
        $message =  'PHPMailer library is not available.';
        \Drupal::logger('mz_email')->error($message);
        return false ;
    }
    return false ;
}
function verificationMail($email){
    list($user, $domain) = explode('@', $email);
    if (!checkdnsrr($domain, 'MX')) {
        \Drupal::logger('mz_email')->error("Email Domain does not have an MX record.");
        return false ;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        \Drupal::logger('mz_email')->error("E-mail is not valid");
        return false ;
    
    }
    return true ;

}

}
