<?php

namespace Drupal\spalp\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Component\Serialization\Json;
use Drupal\spalp\Service\Core;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * JSON Config Form widget.
 *
 * @FieldWidget(
 *   id = "json_config_form_field",
 *   label = @Translation("JsonConfigForm"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class JsonConfigFormWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * Spalp Core Service.
   *
   * @var \Drupal\spalp\Service\Core
   */
  protected $spalpCoreService;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, Core $splap_core_service) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->spalpCoreService = $splap_core_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('spalp.core')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $json_schema = '{}';
    $field_name = $this->fieldDefinition->getName();
    $identifier = $field_name . '-' . $delta . '-jsonconfigform';
    $selector = '[data-drupal-selector="' . Html::getId('edit-' . $field_name . '-' . $delta . '-value') . '"]';
    $app_id = $form['field_spalp_app_id']['widget']['#default_value'][0];
    if (!empty($app_id)) {
      $json_schema = $this->spalpCoreService->getConfigFromJson($app_id, 'schema');
    }
    $element['value'] = $element + [
      '#type' => 'textarea',
      '#suffix' => '<div id="' . $identifier . '"></div>',
      '#default_value' => isset($items[$delta]) ? $items[$delta]->value : '',
      '#rows' => $this->getSetting('rows'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#attributes' => ['class' => ['js-text-full', 'text-full']],
    ];
    $element['#element_validate'][] = [static::class, 'validateJsonConfiguration'];

    $element[$delta] = [
      '#attached' => [
        'library' => [
          'spalp/json-form',
          'spalp/spalp-json-form-builder',
        ],
        'drupalSettings' => [
          'spalpJsonFormBuilder' => [
            $identifier => [
              'schema' => $json_schema,
              'identifier' => $identifier,
              'textarea' => $selector,
            ],
          ],
        ],
      ],
    ];
    return $element;
  }

  /**
   * Validates the input to see if it is a properly formatted JSON object.
   *
   * @inheritdoc
   */
  public static function validateJsonConfiguration(&$element, FormStateInterface $form_state, $form) {
    if (Unicode::strlen($element['value']['#value'])) {
      Json::decode($element['value']['#value']);
      if (json_last_error() !== JSON_ERROR_NONE) {
        $form_state->setError($element['value'], t('!name must contain a valid data.', ['!name' => $element['#title']]));
      }
    }
  }

}
