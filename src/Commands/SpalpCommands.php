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

    // If we don't have a module from the command, ask for one.
    if (empty($module)) {
      $modules = $this->spalpConfig->getAppIds();
      $module = $this->io()->choice('Which module would you like to import configuration for?', $modules);
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

  /**
   * Show the difference between config on the applanding node and in JSON.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drush\Exceptions\UserAbortException
   *
   * @command spalp:config-diff
   * @aliases scd
   */
  public function configDiff($module = '') {
    // If we don't have a module from the command, ask for one.
    if (empty($module)) {
      $modules = $this->spalpConfig->getAppIds();
      $module = $this->io()->choice('Which module would you like to import configuration for?', $modules);
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

    $diff = $this->spalpCore->getAppConfigDiff($module);
    $this->logger()->success(dt('Configuration diff between node @nid and JSON for @module: @diff', [
      '@nid' => $node->id(),
      '@module' => $module,
      '@diff' => print_r($diff, 1),
    ]));

  }

  /**
   * Output the current config on the applanding node for a module.
   *
   * @param string $module
   *   The machine name of the module.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drush\Exceptions\UserAbortException
   *
   * @command spalp:config-export
   * @aliases sce
   */
  public function configExport($module = '') {
    // If we don't have a module from the command, ask for one.
    if (empty($module)) {
      $modules = $this->spalpConfig->getAppIds();
      $module = $this->io()->choice('Which module would you like to import configuration for?', $modules);
    }

    $node = $this->spalpCore->getAppNode($module);
    if (empty($node)) {
      throw new \Exception(dt('There is no app landing node for @module.', [
        '@module' => $module,
      ]));
    }

    $config = $this->spalpCore->getAppConfig($module);

    // TODO: output to a file?
    $this->logger()->success(dt('Current config on node @nid for @module: @config', [
      '@nid' => $node->id(),
      '@module' => $module,
      '@config' => Json::encode($config),
    ]));

  }

}
