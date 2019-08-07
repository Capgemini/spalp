<?php

namespace Drupal\spalp\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
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
   * Language Manager instance.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * ConfigurationJsonController constructor.
   *
   * @param \Drupal\spalp\Service\Core $spalp_core_service
   *   Spalp core service to get app configurations and texts.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   Language manager instance.
   */
  public function __construct(Core $spalp_core_service, LanguageManagerInterface $language_manager) {
    $this->spalpCoreService = $spalp_core_service;
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('spalp.core'), $container->get('language_manager'));
  }

  /**
   * {@inheritdoc}
   */
  public function access(AccountInterface $account, $app_id = '', $revision = NULL) {

    $node = $this->spalpCoreService->getAppNode($app_id);

    // Can the user access the node?
    $access = $node->access('view');

    if ($revision) {
      // Can the user access this revision?
      $access = $account->hasPermission('view applanding revisions');
    }

    return AccessResult::allowedIf($access)->cachePerPermissions()->addCacheableDependency($node);
  }

  /**
   * Display app landing page node data as JSON.
   *
   * @param string $app_id
   *   The machine name of the extending module.
   * @param int $revision
   *   The ID of a specific revision to load.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON output.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function data($app_id = '', $revision = NULL) {
    if (empty($app_id)) {
      throw new NotFoundHttpException();
    }

    $language = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

    $response = $this->spalpCoreService->getAppConfig($app_id, $language, $revision);

    return new JsonResponse($response);
  }

}
