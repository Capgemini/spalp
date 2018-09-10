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
      $additional_config = [
        'links' => [
          'self' => 'http://example.com',
        ],
      ];
      $merged_value = array_merge($config, $additional_config);
      $event->setConfig($merged_value);
    }
  }

}
