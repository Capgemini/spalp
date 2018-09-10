<?php

namespace Drupal\spalp_example\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\spalp\Service\Core;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\spalp_example\SpalpExampleInterface;

/**
 * Class ConfigurationJsonController.
 *
 * @package Drupal\spalp_example\Controller
 */
class ConfigurationJsonController extends ControllerBase {

  /**
   * Spalp core service instance.
   *
   * @var \Drupal\spalp\Service\Core
   */
  protected $spalpCoreService;

  /**
   * ConfigurationJsonController constructor.
   *
   * @param \Drupal\spalp\Service\Core $spalp_core_service
   *   Spalp core service to get app configurations and texts.
   */
  public function __construct(Core $spalp_core_service) {
    $this->spalpCoreService = $spalp_core_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('spalp.core'));
  }

  /**
   * Gets app configuration using spalp core service.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with app configurations based on language and app id
   */
  public function getExampleConfig() {
    $response = $this->spalpCoreService->getAppConfig(
      SpalpExampleInterface::APP_ID,
      \Drupal::languageManager()->getCurrentLanguage()->getId()
    );
    return new JsonResponse($response);
  }

}
