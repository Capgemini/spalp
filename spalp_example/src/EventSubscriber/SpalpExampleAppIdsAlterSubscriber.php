<?php

namespace Drupal\spalp_example\EventSubscriber;

use Drupal\spalp\Event\SpalpAppIdsAlterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SpalpExampleAppIdsAlterSubscriber.
 *
 * @package Drupal\spalp\EventSubscriber
 */
class SpalpExampleAppIdsAlterSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      SpalpAppIdsAlterEvent::EVENT_APP_IDS_ALTER => 'doAppIdsListAlter',
    ];
  }

  /**
   * React to app id alter event to add this modules app id.
   *
   * @param \Drupal\spalp\Event\SpalpAppIdsAlterEvent $event
   */
  public function doAppIdsListAlter(SpalpAppIdsAlterEvent $event) {
    $event->ids[SPALP_EXAMPLE_APP_ID] = SPALP_EXAMPLE_APP_ID;
  }

}
