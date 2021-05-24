<?php

namespace Drupal\spalp\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event that is fired when there is request for collecting available app ids.
 */
class SpalpAppIdsAlterEvent extends Event {

  const APP_IDS_ALTER = 'spalp_app_ids_alter';

  /**
   * List of app ids.
   *
   * @var array
   */
  protected $ids = [];

  /**
   * SpalpAppIdsAlterEvent constructor.
   *
   * @param array $ids
   *   App ids.
   */
  public function __construct(array $ids) {
    $this->ids = $ids;
  }

  /**
   * Gets app ids.
   *
   * @return array
   *   List of app ids.
   */
  public function getIds() {
    return $this->ids;
  }

  /**
   * Adds app id.
   *
   * @param string $id
   *   App id.
   */
  public function registerId($id) {
    $this->ids[$id] = $id;
  }

  /**
   * Sets app ids.
   *
   * @param array $ids
   *   List of app ids.
   */
  public function setIds(array $ids) {
    $this->ids = $ids;
  }

}
