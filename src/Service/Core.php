<?php

namespace Drupal\spalp\Service;

use Drupal\spalp\Event\SpalpConfigAlterEvent;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Spalp Core Service.
 *
 * @package Drupal\spalp\Service
 */
class Core {

  use StringTranslationTrait;

  /**
   * Logger Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $loggerFactory;

  /**
   * Module Handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Event Dispatcher.
   *
   * @var \Drupal\Core\Extension\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Spalp Core constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module Handler Interface.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   Event Dispatcher interface.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory,
                              ModuleHandlerInterface $moduleHandler,
                              EventDispatcherInterface $event_dispatcher) {

    // Logger Factory.
    $this->loggerFactory = $loggerFactory;

    // Module Handler.
    $this->moduleHandler = $moduleHandler;

    // Event Dispatcher.
    $this->eventDispatcher = $event_dispatcher;

  }

  /**
   * Create applanding nodes in each language when a module is enabled.
   *
   * @param string $module
   *   The machine name of the module being installed.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNodes($module) {
    // TODO: get config from YAML.
    // TODO: translate the node.
    $title = $this->moduleHandler->getName($module);

    $node = Node::create(['type' => 'applanding']);
    $node->set('title', $title);
    $node->set('field_spalp_app_id', $module);
    // The node should initially be unpublished.
    $node->status = 0;
    $node->enforceIsNew();
    $node->save();

    $this->loggerFactory->get('spalp')->notice(
      $this->t('Node @nid has been created for @title (@module)',
        [
          '@title' => $title,
          '@module' => $module,
          '@nid' => $node->id(),
        ]
      )
    );
  }

  /**
   * Get the current text and configuration settings for an app.
   *
   * @param string $module
   *   The machine name of the module being installed.
   * @param string $language
   *   The language code.
   *
   * @return string
   *   The text and configuration settings for the app, as JSON.
   */
  public function getAppConfig($module, $language) {
    // Get the relevant node for the app.
    $node = $this->getAppNode($module, $language);

    $config = !empty($node->get('field_spalp_config_json')
      ->getValue()) ? $node->get('field_spalp_config_json')->getValue() : NULL;

    // Instantiate the event and dispatch for changes.
    $event = new SpalpConfigAlterEvent($config);
    // Set app id for this event.
    $event->setAppId($module);
    $this->eventDispatcher->dispatch(SpalpConfigAlterEvent::APP_CONFIG_ALTER, $event);
    return json_encode($event->getConfig());
  }

  /**
   * Get the relevant node for a single page app.
   *
   * @param string $module
   *   The machine name of the extending module.
   * @param string $language
   *   The language code.
   */
  public function getAppNode($module, $language) {
    $node_details = [];
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'applanding')
      ->condition('field_spalp_app_id', $module)
      ->execute();
    $nid = end($nids);
    $node = Node::load($nid);

    $node_details = $node->getTranslation($language);
    return $node_details;
  }

}
