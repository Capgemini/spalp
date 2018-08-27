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
    const APP_ID = "spalp_example";

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
      $event->ids[self::APP_ID] = self::APP_ID;
  }

}
