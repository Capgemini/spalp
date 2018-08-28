<?php

namespace Drupal\spalp\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event that is fired when there is request for collecting available app ids.
 */
class SpalpAppIdsAlterEvent extends Event {

  const EVENT_APP_IDS_ALTER = 'spalp_app_ids_alter';

  /**
   * List of app ids.
   */
  public $ids;

  /**
   * Constructs the object.
   */
  public function __construct($ids) {
    $this->ids = $ids;
  }

}
