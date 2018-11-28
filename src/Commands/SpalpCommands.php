<?php

namespace Drupal\spalp\Commands;

use Drupal\Component\Serialization\Json;
use Drupal\spalp\Service\Core;
use Drupal\spalp\Service\SpalpConfig;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for spalp.
 */
class SpalpCommands extends DrushCommands {

  /**
   * Spalp Core Service.
   *
   * @var \Drupal\spalp\Service\Core
   */
  protected $spalpCore;

  /**
   * Spalp Config Service.
   *
   * @var \Drupal\spalp\Service\SpalpConfig
   */
  private $spalpConfig;

  /**
   * SpalpCommands constructor.
   *
   * @param \Drupal\spalp\Service\Core $spalpCore
   *   Spalp Core Service.
   * @param \Drupal\spalp\Service\SpalpConfig $spalpConfig
   *   Spalp Config Service.
   */
  public function __construct(Core $spalpCore, SpalpConfig $spalpConfig) {
    $this->spalpCore = $spalpCore;
    $this->spalpConfig = $spalpConfig;
  }

  /**
   * Re-import JSON from config file to an app landing node.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drush\Exceptions\UserAbortException
   *
   * @usage spalp:import-json spalp_example
   *   Re-import JSON for the spalp_example module.
   *
   * @command spalp:import-json
   * @aliases sij
   */
  public function importJson($module = '') {

    if (empty($module)) {
      $modules = $this->spalpConfig->getAppIds();
      $module = $this->io()->choice('Enter the module name to import', $modules);
    }

    $json = $this->spalpCore->getConfigFromJson($module);
    if (empty($json)) {
      throw new \Exception(dt('@module does not provide JSON configuration.', [
        '@module' => $module,
      ]));
    }

    $node = $this->spalpCore->getAppNode($module);
    if (empty($node)) {
      throw new \Exception(dt('There is no app landing node for @module.', [
        '@module' => $module,
      ]));
    }

    $config_json = Json::encode($json);
    $node->set('field_spalp_config_json', $config_json);
    $node->save();

    $this->logger()->success(dt('Configuration imported to node @nid for @module.', [
      '@nid' => $node->id(),
      '@module' => $module,
    ]));
  }

}
