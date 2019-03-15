<?php

namespace Drupal\spalp\Service;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\spalp\Event\SpalpConfigAlterEvent;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\spalp\Event\SpalpConfigLocationAlterEvent;
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
   * The Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Event Dispatcher.
   *
   * @var \Drupal\Core\Extension\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Language Manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * Spalp Core constructor.
   *
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerFactory
   *   LoggerChannelFactory.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   Module Handler Interface.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   Event Dispatcher interface.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   EntityTypeManagerInterface.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   LanguageManagerInterface.
   */
  public function __construct(
    LoggerChannelFactoryInterface $loggerFactory,
    ModuleHandlerInterface $moduleHandler,
    EventDispatcherInterface $event_dispatcher,
    EntityTypeManagerInterface $entity_type_manager,
    LanguageManagerInterface $language_manager
  ) {

    $this->loggerFactory = $loggerFactory;
    $this->moduleHandler = $moduleHandler;
    $this->eventDispatcher = $event_dispatcher;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;

  }

  /**
   * Create applanding nodes in each language when a module is enabled.
   *
   * @param string $module
   *   The machine name of the module being installed.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function createNodes($module) {
    // TODO: get config from YAML.
    // TODO: translate the node.
    $title = $this->moduleHandler->getName($module);

    // Import the configuration and text.
    $json = $this->getConfigFromJson($module);
    if (!empty($json)) {
      $node = $this->entityTypeManager->getStorage('node')->create(['type' => 'applanding']);

      $node->set('title', $title);
      $node->set('field_spalp_app_id', $module);

      $config_json = Json::encode($json);
      $node->set('field_spalp_config_json', $config_json);

      // The node should initially be unpublished.
      $node->status = 0;
      $node->enforceIsNew();
      $node->save();

      // TODO: translate the node.
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
  public function getConfigFromJson($module, $type = 'config') {
    $json = [];

    // Set up default paths to config files.
    $module_path = DRUPAL_ROOT . '/' . drupal_get_path('module', $module);
    $config_locations = [
      'config' => $module_path . "/{$module}.config.json",
      'schema' => $module_path . "/{$module}.config.schema.json",
    ];

    // Allow modules to change the config path.
    $event = new SpalpConfigLocationAlterEvent($module, $config_locations);
    $this->eventDispatcher->dispatch(SpalpConfigLocationAlterEvent::CONFIG_LOCATION_ALTER, $event);
    $config_locations = $event->getConfigLocations();

    // Get the JSON from the file.
    $filename = $config_locations[$type];
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
   *   The machine name of the module being installed.
   * @param string $language
   *   The language code.
   * @param int $revision
   *   The ID of a specific revision to load.
   *
   * @return array
   *   The text and configuration settings for the app json endpoint, as array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAppConfig($module, $language = NULL, $revision = NULL) {
    $config = [];

    if ($language == NULL) {
      $language = $this->languageManager->getCurrentLanguage()->getId();
    }

    // Get the relevant node for the app.
    $node = $this->getAppNode($module, $language, $revision);
    if (!empty($node)) {

      // TODO: check permission to view the node and revision.
      $config_json = $node->field_spalp_config_json->value;
      $config = Json::decode($config_json);

      // Dispatch event to allow modules to change config.
      $event = new SpalpConfigAlterEvent($module, $config);
      $this->eventDispatcher->dispatch(SpalpConfigAlterEvent::APP_CONFIG_ALTER, $event);

      $config = $event->getConfig();
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
   * @param int $revision
   *   The ID of a specific revision to load.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The applanding node for this module.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAppNode($module, $language = NULL, $revision = NULL) {
    $node_storage = $node = $this->entityTypeManager->getStorage('node');

    $query = $node_storage->getQuery()
      ->condition('type', 'applanding')
      ->condition('field_spalp_app_id', $module);
    $nids = $query->execute();

    if (!empty($nids)) {
      // TODO: prevent more than 1 node per language being created for each app.
      $nid = end($nids);
      $node = $node_storage->load($nid);

      if (!empty($revision)) {
        $node = $node_storage->loadRevision($revision);
      }

      try {
        // Use the translation, if there is one.
        $node = $node->getTranslation($language);
      }
      catch (\InvalidArgumentException $exception) {
        // If there's no relevant translation, log it.
        $this->loggerFactory->get('spalp')->notice(
          $this->t('Attempt to fetch non-existent translation of node @nid to @language for @module module.',
            [
              '@nid' => $node->id(),
              '@language' => $language,
              '@module' => $module,
            ]
          )
        );
      }
    }

    return $node;
  }

  /**
   * Prepare a link to the page head with the app's JSON endpoint URL.
   *
   * @param string $app_id
   *   The machine name of the extending module.
   * @param int $revision
   *   The node revision ID.
   *
   * @return array
   *   Render array for the link.
   */
  public function getJsonLink($app_id, $revision = NULL) {
    $parameters = ['app_id' => $app_id];
    if (!empty($revision)) {
      $parameters['revision'] = $revision;
    }
    $config_url = Url::fromRoute('entity.node.appjson', $parameters)->toString();
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

  /**
   * Set the configuration settings for an app.
   *
   * @param string $module
   *   The machine name of the module.
   * @param array $config_json
   *   The configuration settings.
   * @param bool $overwrite
   *   Whether to overwrite existing values on the node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setAppConfig($module, array $config_json = NULL, $overwrite = FALSE) {

    // Get config from JSON file if none is provided.
    if ($config_json === NULL) {
      $config_json = $this->getConfigFromJson($module);

      if (empty($config_json)) {
        throw new \Exception(dt('@module does not provide JSON configuration.', [
          '@module' => $module,
        ]));
      }
    }

    $node = $this->getAppNode($module);
    if (empty($node)) {
      throw new \Exception(dt('There is no app landing node for @module.', [
        '@module' => $module,
      ]));
    }

    // Get existing config from the applanding node.
    $config_node = $this->getAppConfig($module);
    $config = $this->newAppConfig($config_node, $config_json, $overwrite);

    $node->set('field_spalp_config_json', Json::encode($config));
    $node->save();

  }

  /**
   * @param array $config_node
   *   The current configuration on the applanding node.
   * @param array $config_json
   *   The configuration
   * @param bool $overwrite
   *
   * @return array
   *   The merged configuration array.
   */
  public function newAppConfig($config_node, $config_json, $overwrite = FALSE) {
    // Merge the existing and new configuration.
    if ($overwrite) {
      // Overwrite node values with values from JSON.
      $config = NestedArray::mergeDeepArray([$config_node, $config_json], TRUE);
    }
    else {
      // Retain values in the node.
      $config = NestedArray::mergeDeepArray([$config_json, $config_node], TRUE);
    }

    return $config;
  }

  /**
   * Get the difference between config in the JSON file and the applanding node.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @return array
   *   Associative array of differences.
   *   'node_only': in the node, but not in JSON for the module.
   *   'json_only': in JSON, but not in the node.
   *   'diff': in both arrays, with different values.
   *   - 'node': the value on the node.
   *   - 'json': the value in JSON.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getAppConfigDiff($module) {
    $config_node = $this->getAppConfig($module);
    $config_json = $this->getConfigFromJson($module);

    $diff = $this->arrayDiffRecursive($config_node, $config_json);

    return array_filter($diff);
  }

  /**
   * Recursively compare two arrays.
   *
   * @param array $config_node
   *   The configuration array from the applanding node.
   * @param array $config_json
   *   The configuration array from the JSON file.
   *
   * @return array
   *   Associative array of differences.
   *   'node_only': in $config_node, but not $config_json
   *   'json_only': in $config_json, but not $config_node
   *   'diff': in both arrays, with different values.
   *   - 'node': the value in $config_node
   *   - 'json': the value in $config_json
   */
  public function arrayDiffRecursive(array $config_node, array $config_json) {
    $result = ['node_only' => [], 'json_only' => [], 'diff' => []];
    foreach ($config_node as $key => $value) {
      if (is_array($value) && isset($config_json[$key]) && is_array($config_json[$key])) {
        $sub_result = $this->arrayDiffRecursive($value, $config_json[$key]);
        foreach (array_keys($sub_result) as $sub_key) {
          if (!empty($sub_result[$sub_key])) {
            $result[$sub_key] = array_merge_recursive($result[$sub_key],
              [$key => $sub_result[$sub_key]]);
          }
        }
      }
      else {
        if (isset($config_json[$key])) {
          if ($value !== $config_json[$key]) {
            $result['diff'][$key] = [
              'node' => $value,
              'json' => $config_json[$key],
            ];
          }
        }
        else {
          $result['node_only'][$key] = $value;
        }
      }
    }
    foreach ($config_json as $key => $value) {
      if (!isset($config_node[$key])) {
        $result['json_only'][$key] = $value;
      }
    }
    return $result;
  }

}
