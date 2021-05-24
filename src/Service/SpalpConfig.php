<?php

namespace Drupal\spalp\Service;

use Drupal\spalp\Event\SpalpAppIdsAlterEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Spalp Config Service.
 *
 * @package Drupal\spalp\Service
 */
class SpalpConfig {

  /**
   * An event dispatcher instance to use for configuration events.
   *
   * @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * SpalpConfig constructor.
   *
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $event_dispatcher
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
    $this->eventDispatcher->dispatch($event, SpalpAppIdsAlterEvent::APP_IDS_ALTER);

    // Return updated ids from event.
    return $event->getIds();
  }

}
