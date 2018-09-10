<?php

namespace Drupal\spalp\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event fired for collecting additional configurations for this app id.
 */
class SpalpConfigAlterEvent extends Event {

  const APP_CONFIG_ALTER = 'spalp_config_alter';

  /**
   * List of app config.
   *
   * @var array
   */
  protected $config = [];

  /**
   * App id for the sub module.
   *
   * @var string
   */
  protected $appId;

  /**
   * SpalpConfigAlterEvent constructor.
   *
   * @param array $config
   *   App config.
   */
  public function __construct(array $config) {
    $this->config = $config;
  }

  /**
   * Gets app config.
   *
   * @return array
   *   List of app config.
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Sets app config.
   *
   * @param array $config
   *   List of app config.
   */
  public function setConfig(array $config) {
    $this->config = $config;
  }

  /**
   * Set app id for the configuration event.
   *
   * @param string $appId
   *   App id defined for sub-module.
   */
  public function setAppId($appId) {
    $this->appId = $appId;
  }

  /**
   * Get app id for this event for sub-modules to check.
   *
   * @return string
   *   app id fot the event
   */
  public function getAppId() {
    return $this->appId;
  }

}
