<?php

namespace Drupal\spalp\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Provides settings for spalp module.
 */
class SpalpConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'spalp.settings',
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'spalp_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config('spalp.settings');

    $form['json_forms_enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable JSON Forms'),
      '#default_value' => $config->get('json_forms_enabled'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('spalp.settings')
      ->set('json_forms_enabled', $form_state->getValue('json_forms_enabled'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
