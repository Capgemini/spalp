<?php

namespace Drupal\spalp\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * Event fired to allow modules to change app configuration.
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
   * App id for the extending module.
   *
   * @var string
   */
  protected $appId;

  /**
   * SpalpConfigAlterEvent constructor.
   *
   * @param string $app_id
   *   The machine name of the extending module.
   * @param array $config
   *   App config.
   */
  public function __construct($app_id, array $config) {
    $this->appId = $app_id;
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
   *   List of configuration options.
   */
  public function setConfig(array $config) {
    $this->config = $config;
  }

  /**
   * Get app id for this event for sub-modules to check.
   *
   * @return string
   *   App id fot the event.
   */
  public function getAppId() {
    return $this->appId;
  }

}
