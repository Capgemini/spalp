<?php

namespace Drupal\spalp\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
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
  public function __construct(LoggerChannelFactoryInterface $loggerFactory, ModuleHandlerInterface $moduleHandler) {

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
    if (!empty($json)) {
      $config_json = Json::encode($json);
      $node->set('field_spalp_config_json', $config_json);
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
      $json = Json::decode($string);
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

      // TODO: check permission to view the node.
      // TODO: get a specific revision.
      $config = $node->field_spalp_config_json->value;
    }

    return Json::decode($config);
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
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'applanding')
      ->condition('field_spalp_app_id', $module);

    $nids = $query->execute();

    $nid = end($nids);
    $node_storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $node_storage->load($nid);
    $node_details = $node->getTranslation($language);

    return $node_details;
  }

  /**
   * Prepare a link to the page head with the app's JSON endpoint URL.
   *
   * @param string $app_id
   *   The machine name of the extending module.
   *
   * @return array
   *   Render array for the link.
   */
  public function getJsonLink($app_id) {
    // TODO: change the link if we're on a revision ID.
    $config_url = Url::fromRoute('entity.node.appjson', ['app_id' => $app_id])->toString();
    $config_json = [
      [
        'type' => 'application/json',
        'id' => 'appConfig',
        'rel' => 'alternate',
        'href' => $config_url,
      ],
      TRUE,
    ];

    return $config_json;
  }

}
