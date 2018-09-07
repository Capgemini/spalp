<?php

namespace Drupal\spalp\Service;

use Drupal\spalp\Event\SpalpAppIdsAlterEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Spalp Config Service.
 *
 * @package Drupal\spalp\Service
 */
class SpalpConfig {

  /**
   * An event dispatcher instance to use for configuration events.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * SpalpConfig constructor.
   *
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   Injected event dispatcher dependency.
   */
  public function __construct(EventDispatcherInterface $event_dispatcher) {
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * Method to get app ids list.
   *
   * @return array
   *   List of app ids.
   */
  public function getAppIds() {
    // Reset app ids.
    $ids = [];

    // Instantiate the event and dispatch for changes.
    $event = new SpalpAppIdsAlterEvent($ids);
    $this->eventDispatcher->dispatch(SpalpAppIdsAlterEvent::APP_IDS_ALTER, $event);

    // Return updated ids from event.
    return $event->getIds();
  }

  /**
   * Get json schmea config from the module's JSON file.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @return array
   *   Array representation of the configuration schema settings.
   *
   * @todo Merge this method with getConfigFromJson method present on pull request #7
   */
  public function getConfigSchemaJson($module) {
    $json = [];
    // Get the JSON file for the module.
    $filename = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . "/$module.config.schema.json";
    if (file_exists($filename)) {
      $string = file_get_contents($filename);
      $json = json_decode($string);
    }
    return $json;
  }

}
