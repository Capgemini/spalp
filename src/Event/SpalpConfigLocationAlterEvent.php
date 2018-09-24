<?php

namespace Drupal\spalp\Event;

use Symfony\Component\EventDispatcher\Event;

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
  public function __construct($appId, $configLocations) {
    $this->appId = $appId;
    $this->configLocations = $configLocations;
  }

  /**
   * @return mixed
   */
  public function getAppId() {
    return $this->appId;
  }

  /**
   * @param mixed $appId
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * @return mixed
   */
  public function getConfigLocations() {
    return $this->configLocations;
  }

  /**
   * @param mixed $configLocations
   */
  public function setConfigLocations($configLocations) {
    $this->configLocations = $configLocations;
  }

}
