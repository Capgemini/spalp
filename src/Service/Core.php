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
    $title = $this->moduleHandler->getName($module);

    $node = Node::create(['type' => 'applanding']);
    $node->set('title', $title);
    $node->set('field_spalp_app_id', $module);

    // Import the configuration and text.
    $json = $this->getConfigFromJson($module);

    // TODO: translate the node.
    $language = 'en';
    if (!empty($json)) {
      $app_text = $json->appText;
      $app_text_string = json_encode($app_text->{$language});
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
   * @param string $type
   *   Type to be used for schema json calls.
   *
   * @return array
   *   Array representation of the configuration settings.
   */
  public function getConfigFromJson($module, $type = NULL) {
    $json = [];

    $type = $type !== NULL ? '.' . $type : '';

    // Get the JSON file for the module.
    $filename = DRUPAL_ROOT . '/' . drupal_get_path('module', $module) . "/{$module}.config{$type}.json";
    if (file_exists($filename)) {
      $string = file_get_contents($filename);
      $json = json_decode($string);
    }

    return $json;
  }

  /**
   * Get the current text and configuration settings for an app.
   *
   * @param string $module
   *   The machine name of the extending module.
   * @param string $language
   *   The language code.
   *
   * @return string
   *   The text and configuration settings for the app, as JSON.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAppConfig($module, $language) {

    $config = new \StdClass();

    // Get the relevant node for the app.
    $node = $this->getAppNode($module, $language);
    if (!empty($node)) {
      $app_config = $node->field_spalp_app_config->value;
      $app_text = $node->field_spalp_app_text->value;

      $config->appConfig = json_decode($app_config);
      $config->appText = json_decode($app_text);
    }

    return $config;
  }

  /**
   * Get the relevant node for a single page app.
   *
   * @param string $module
   *   The machine name of the extending module.
   * @param string $language
   *   The language code.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The relevant applanding node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAppNode($module, $language) {
    // TODO: dependency injection.
    // TODO: prevent more than one node per language being created for each app.
    // TODO: filter by language.
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'applanding')
      ->condition('field_spalp_app_id', $module);

    $nids = $query->execute();

    $nid = end($nids);
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $node_storage->load($nid);

    return $node;
  }

}
