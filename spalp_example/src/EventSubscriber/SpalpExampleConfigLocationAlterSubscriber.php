<?php

namespace Drupal\spalp_example\EventSubscriber;

use Drupal\spalp\Event\SpalpConfigLocationAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SpalpExampleConfigLocationAlterSubscriber.
 *
 * @package Drupal\spalp_example\EventSubscriber
 */
class SpalpExampleConfigLocationAlterSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SpalpConfigLocationAlterEvent::CONFIG_LOCATION_ALTER] = 'doConfigLocationAlter';
    return $events;
  }

  /**
   * Use a custom location for spalp configuration.
   *
   * @param \Drupal\spalp\Event\SpalpConfigLocationAlterEvent $event
   *   Spalp Config Location Alter event.
   */
  public function doConfigLocationAlter(SpalpConfigLocationAlterEvent $event) {
    if ($event->getAppId() == 'spalp_example') {
      // These are the default locations.
      // In a real implementation, you would change them.
      // For instance, your JSON might be in the libraries directory.
      // TODO: proper dependency injection example.
      $module_path = drupal_get_path('module', 'spalp_example');
      $locations = [
        'config' => $module_path . '/config/spalp/spalp_example.config.json',
        'schema' => $module_path . '/config/spalp/spalp_example.config.schema.json',
      ];

      $event->setConfigLocations($locations);
    }
  }

}
