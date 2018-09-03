<?php

namespace Drupal\spalp\Service;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;

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
   * Spalp Core constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module Handler Interface.
   */
  public function __construct(LoggerChannelFactoryInterface $loggerFactory,
                              ModuleHandlerInterface $moduleHandler) {

    // Logger Factory.
    $this->loggerFactory = $loggerFactory;

    // Module Handler.
    $this->moduleHandler = $moduleHandler;

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

    // Import the configuration and text.
    $json = $this->getConfigFromJson($module);
    if (!empty($json)) {
      $app_text_string = json_encode($json->appText);
      $node->set('field_spalp_app_text', $app_text_string);

      $app_config_string = json_encode($json->appConfig);
      $node->set('field_spalp_app_config', $app_config_string);
    }

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
   * Get initial config from the module's JSON file.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @return array
   *   Array representation of the configuration settings.
   */
  public function getConfigFromJson($module) {
    $json = [];

    // Get the JSON file for the module.
    $filename = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . "/$module.config.json";

    if (file_exists($filename)) {
      $string = file_get_contents($filename);
      $json = json_decode($string);
    }

    return $json;
  }

}
