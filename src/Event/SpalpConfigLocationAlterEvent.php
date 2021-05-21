<?php

namespace Drupal\spalp\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Class SpalpConfigLocationAlterEvent.
 *
 * @package Drupal\spalp\Event
 */
class SpalpConfigLocationAlterEvent extends Event {

  const CONFIG_LOCATION_ALTER = 'spalp_config_location_alter';

  /**
   * App id for the extending module.
   *
   * @var string
   */
  protected $appId;

  /**
   * Paths to config files, keyed by type.
   *
   * @var array
   */
  protected $configLocations;

  /**
   * SpalpConfigLocationAlterEvent constructor.
   *
   * @param string $appId
   *   The app ID for the extending module.
   * @param array $configLocations
   *   Array of file locations, keyed by type.
   */
  public function __construct($appId, array $configLocations) {
    $this->appId = $appId;
    $this->configLocations = $configLocations;
  }

  /**
   * Get the app ID for this event.
   *
   * @return string
   *   The app ID.
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * Set the app ID for this event.
   *
   * @param string $appId
   *   The app ID.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get the paths of the JSON config files.
   *
   * @return array
   *   Array of paths, keyed by type.
   */
  public function getConfigLocations() {
    return $this->configLocations;
  }

  /**
   * Set the paths of the JSON config files.
   *
   * @param mixed $configLocations
   *   Array of paths, keyed by type.
   */
  public function setConfigLocations($configLocations) {
    $this->configLocations = $configLocations;
  }

}
