<?php

namespace Drupal\spalp_example\EventSubscriber;

use Drupal\spalp\Event\SpalpConfigAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\spalp_example\SpalpExampleInterface;

/**
 * Class SpalpExampleConfigAlterSubscriber.
 *
 * @package Drupal\spalp_example\EventSubscriber
 */
class SpalpExampleConfigAlterSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SpalpConfigAlterEvent::APP_CONFIG_ALTER] = 'doAppConfigAlter';

    return $events;
  }

  /**
   * React to app alter event to add additional config.
   *
   * @param \Drupal\spalp\Event\SpalpConfigAlterEvent $event
   *   Spalp App Ids Alter Event.
   */
  public function doAppConfigAlter(SpalpConfigAlterEvent $event) {
    if ($event->getAppId() === SpalpExampleInterface::APP_ID) {
      $config = $event->getConfig();

      // Simple example - change one setting on test environments.
      if ($this->isTestEnvironment()) {
        $config['appConfig']['bodyRepeat'] = 5;
      }

      $event->setConfig($config);
    }
  }

  /**
   * Check if we're on a test environment.
   *
   * @return bool
   *   TRUE if we're on a test environment.
   */
  public function isTestEnvironment() {
    // TODO: proper dependency injection example.
    $host = \Drupal::request()->getHost();

    $test_environments = [
      'localhost',
      'dev.example.com',
      'test.example.com',
    ];

    return in_array($host, $test_environments);
  }

}
