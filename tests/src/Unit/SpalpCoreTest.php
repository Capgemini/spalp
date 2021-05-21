<?php

namespace Drupal\Tests\spalp\Unit;

use Drupal\spalp\Service\Core;
use Drupal\Tests\UnitTestCase;


/**
 * Unit tests for common utilities functions.
 *
 * @coversDefaultClass \Drupal\spalp\Service\Core
 * @group spalp
 */
class SpalpCoreTest extends UnitTestCase {

  public $coreService;

  public function setUp() {
    parent::setUp();
    $loggerFactory = $this->createMock('\Drupal\Core\Logger\LoggerChannelFactoryInterface');
    $moduleHandler = $this->createMock('\Drupal\Core\Extension\ModuleHandlerInterface');
    $event_dispatcher = $this->createMock('\Symfony\Contracts\EventDispatcher\EventDispatcherInterface');
    $entity_type_manager = $this->createMock('\Drupal\Core\Entity\EntityTypeManagerInterface');
    $language_manager = $this->getMockBuilder('\Drupal\Core\Language\LanguageManagerInterface')->disableOriginalConstructor()->getMock();

    $this->coreService = new Core(
      $loggerFactory,
      $moduleHandler,
      $event_dispatcher,
      $entity_type_manager,
      $language_manager
    );

  }

  /**
   * Tests newAppConfig().
   *
   * @covers ::newAppConfig
   * @dataProvider setAppConfigDataProvider
   */
  public function testNewAppConfig(
    $nodeConfig,
    $jsonConfig,
    $overwrite,
    $expectedConfig
  ) {
    $newConfig = $this->coreService->newAppConfig($nodeConfig, $jsonConfig,
      $overwrite);
    $this->assertEquals($expectedConfig, $newConfig);
  }

  public function setAppConfigDataProvider() {
    $nodeConfig = [
      'key_1' => 'value 1 in node',
      'key_2' => 'value 2 in node',
      'key_3' => [
        'item 1 in node',
        'item 2 in node',
        'item 3 in node',
      ],
    ];

    $jsonConfig = [
      'key_1' => 'value 1 in json',
      'key_2' => 'value 2 in json',
      'key_3' => [
        'item 1 in json',
        'item 2 in json',
        'item 3 in json',
      ],
    ];

    $nodeConfigAdded = [
      'key_1' => 'value 1 in node',
      'key_2' => 'value 2 in node',
      'key_3' => [
        'item 1 in node',
        'item 2 in node',
        'item 3 in node',
        'item 4 in json',
      ],
      'key_4' => 'value 4 in json',
    ];

    $jsonConfigAdded = [
      'key_1' => 'value 1 in json',
      'key_2' => 'value 2 in json',
      'key_3' => [
        'item 1 in json',
        'item 2 in json',
        'item 3 in json',
        'item 4 in json',
      ],
      'key_4' => 'value 4 in json',
    ];

    return [
      [$nodeConfig, $jsonConfig, FALSE, $nodeConfig],
      [$nodeConfig, $jsonConfig, TRUE, $jsonConfig],
      [$nodeConfig, $jsonConfigAdded, FALSE, $nodeConfigAdded],
      [$nodeConfig, $jsonConfigAdded, TRUE, $jsonConfigAdded],
    ];
  }

}
