<?php

namespace Drupal\mz_email\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiController.
 */
class ApiController extends ControllerBase {

  /**
   * Paragraph_delete.
   *
   * @return string
   *   Return Hello string.
   */
  public function send() {
    $json = [];
    $method = \Drupal::request()->getMethod();
    $id = null;
    if ($method == "POST") {
        $content = \Drupal::request()->getContent();
 
        if (!empty($content) && isset($content["subject"]) && isset($content["to"]) && isset($content["htmlContent"])) {
            $content = json_decode($content, TRUE);        
            if(!isset($content["sender"])){$content["sender"] = null ;}
            $service = \Drupal::service("mz_email.default");
            $is_valid = $service->sendinblue_curl_send($content["subject"],$content["htmlContent"],$content["to"],$content["sender"]) ;
            if($is_valid){
               $message = "Email sent successfully.";
            }else{
                $message = "There was a problem sending your email.";
            }

        }else{
            $message = "Parameters: subject , to , htmlContent are required";
        }
    }else{
        $message = "No POST request";
    }
    $json = ['status'=>$is_valid,'message'=> $message ] ;
    return new JsonResponse($json);
  }
}
