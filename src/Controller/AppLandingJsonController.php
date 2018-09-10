<?php

namespace Drupal\spalp\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\spalp\Service\Core;

/**
 * Class AppLandingJsonController.
 */
class AppLandingJsonController extends ControllerBase {

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
   * Display app landing page node data as JSON.
   *
   * @param string $app_id
   *   The machine name of the extending module.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON output.
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function data($app_id = '') {
    if (empty($app_id)) {
      throw new NotFoundHttpException();
    }

    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $response = $this->spalpCoreService->getAppConfig($app_id, $language);

    return new JsonResponse($response);
  }
}

