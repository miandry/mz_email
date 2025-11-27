<?php

namespace Drupal\mz_email\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class DefaultTwigExtension.
 */
class DefaultTwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getTokenParsers() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getNodeVisitors() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getTests() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      new TwigFunction('mz_email', [static::class, 'mz_email_twig']),
      new TwigFunction('mz_email_api', [static::class, 'mz_email_api_twig']),
    ];
  }

  /**
   * Send email using mz_email.default service.
   */
  public static function mz_email_twig($sentTo, $subject, $body) {
    return \Drupal::service('mz_email.default')->sendMail($sentTo, $subject, $body);
  }

  /**
   * Send email via API (ex: Sendinblue).
   */
  public static function mz_email_api_twig($subject, $htmlContent, $to, $sender = null) {
    $service = \Drupal::service('mz_email.default');
    return $service->sendinblue_curl_send($subject, $htmlContent, $to, $sender);
  }

  /**
   * Twig 3 requires exactly TWO operator arrays (unary + binary).
   * {@inheritdoc}
   */
  public function getOperators() {
    return [
      [], // unary operators
      [], // binary operators
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'mz_email.twig.extension';
  }

}
