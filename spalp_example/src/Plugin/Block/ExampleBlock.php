<?php

namespace Drupal\spalp_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spalp\Service\Core;

/**
 * Provides a block to demonstrate how .
 *
 * @Block(
 *  id = "spalp_example_block",
 *  admin_label = @Translation("Single Page Application Landing Page example block"),
 * )
 */
class ExampleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\spalp\Service\Core definition.
   *
   * @var \Drupal\spalp\Service\Core
   */
  protected $spalpCore;
  /**
   * Constructs a new ExampleBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Core $spalp_core
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->spalpCore = $spalp_core;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('spalp.core')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $config = $this->spalpCore->getAppConfig('spalp_example', $language);

    $app_text = $config->appText->{$language};

    $build['spalp_example_block']['#markup'] = '<h2>' . $app_text->heading . '</h2>' . $app_text->body;

    return $build;
  }

}
