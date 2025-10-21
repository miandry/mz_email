<?php

namespace Drupal\mz_email\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SMTPForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form_mail';
  }
    /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mz_email.settings',
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config("mz_email.settings");
    $form['intro'] = [
      '#markup' => '<pre> URL :  https://app.brevo.com/settings/keys/api </pre>',
    ];
    $form['apiKey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('apiKey'),
      '#default_value' =>  ($config->get('apiKey')) ? $config->get('apiKey') : null ,  
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['sender_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mail Sender Label'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => ($config->get('sender_name')) ? $config->get('sender_name') : "Test mail form mz_email" ,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['sender_mail'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Mail Sender'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => ($config->get('sender_mail')) ? $config->get('sender_mail') : null ,
      '#weight' => '0',
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    $form['submit1'] = [
      '#type' => 'submit',
      '#value' => $this->t('Test email'),
      '#submit' => ['::submitTestEmail']
    ];
  
    return $form;
  }
  public function submitTestEmail(array &$form, FormStateInterface $form_state) {
      
        $to = [
          "mail" => "miandrilala9@yahoo.fr",
          "name" => "Destinataire"
        ];
        $subject =  "Test via cURL Brevo";
        $htmlContent =  "<html><body><h1>Bonjour !</h1><p>Ceci est un test envoyé avec cURL et Brevo API.</p></body></html>";
     $service = \Drupal::service("mz_email.default");
     $service->sendinblue_curl_send($subject,$htmlContent,$to) ;

  } 
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->configFactory()->getEditable("mz_email.settings")
      ->set('apiKey', $form_state->getValue('apiKey'))
      ->set('sender_mail', $form_state->getValue('sender_mail'))
      ->set('sender_name', $form_state->getValue('sender_name'))
      ->save();
  }

}
