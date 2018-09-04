<?php

namespace Drupal\spalp_example\EventSubscriber;

use Drupal\spalp_example\SpalpExampleInterface;
use Drupal\spalp\Event\SpalpAppIdsAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SpalpExampleAppIdsAlterSubscriber.
 *
 * @package Drupal\spalp_example\EventSubscriber
 */
class SpalpExampleAppIdsAlterSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[SpalpAppIdsAlterEvent::APP_IDS_ALTER] = 'doAppIdsListAlter';
    return $events;
  }

  /**
   * React to app id alter event to add this modules app id.
   *
   * @param \Drupal\spalp\Event\SpalpAppIdsAlterEvent $event
   *   Spalp App Ids Alter Event.
   */
  public function doAppIdsListAlter(SpalpAppIdsAlterEvent $event) {
    $event->registerId(SpalpExampleInterface::APP_ID);
  }

}
